<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Models\Product;
use App\Models\Photo;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Match;
use App\Models\Attribute;

use DB;

class ProductC extends Controller
{
    public $mode;

    public function __construct()
    {
        require_once(app_path('Http/Google/api/vendor/autoload.php'));

        $this->mode = 'test';
    }

    public function index(Request $r, $id = null)
    {
        $incoming = $data['incoming'] = $id ? Product::find($id) : new Product;

        $brands = $data['brands'] = $this->brand_list();

        $photos = $data['photos'] = Photo::where('product_id', $incoming->id)->orderBy('order')->get();

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $continue = ['task', '_token', 'photo', 'photo-del', 'profile', 
                    'profile-select', 'bullet', 'attribute', 'category_id_other'];

                foreach ($r->all() as $name => $value)
                {
                    if (in_array($name, $continue))
                    
                        continue;

                    if ($r->has($name))
                    
                        $incoming->$name = $value;
                }

                foreach (['domestic', 'assembly', 'battery'] as $name)

                    $incoming->$name = $r->has($name) ? 1 : 0;

                if (mb_strlen($incoming->pre_description) > 65)

                    return [false, 'Ön açıklama 65 karakterden büyük olamaz.'];

                if (!$incoming->url)
                
                    $incoming->url = Controller::url($incoming);

                if ($r->has('discount'))
                {
                    if ($incoming->price < $r->get('discount'))

                        return [false, 'Fiyatları kontrol edin.'];
                }

                if ($r->has('bullet'))
                {   
                    $temp = array_filter($r->get('bullet'));

                    $incoming->bullet = count($temp) > 0 ? json_encode($temp) : null;
                }

                else $incoming->bullet = null;

                $battery_info = $r->get('battery_info');

                if (!isset($battery_info[0]))

                    $battery_info[0] = 0;

                if (!isset($battery_info[3]))

                    $battery_info[3] = 0;

                $r->merge(['battery_info' => $battery_info]);

                $incoming->battery_info = $r->has('battery_info') ? json_encode($r->get('battery_info')) : null;

                $incoming->category_id_other = $r->has('category_id_other') ? implode(', ', $r->get('category_id_other')) : null;

                try 
                {
                    if ($id)
                    {
                        DB::beginTransaction();

                        DB::connection('commerce')->beginTransaction();
                    }

                    $incoming->save();
                }
                
                catch (\Exception $e) 
                { 
                    return [false, $e->getMessage()];
                }

                try
                {
                    $price_p = $incoming->price;

                    if ($incoming->tax > 0)

                        $price_p = $price_p / (1 + ($incoming->tax / 100));

                    $name_p = $incoming->name_invoice .' '. $incoming->code;

                    if (isset($brands[$incoming->brand_id]))

                        $name_p = str_replace(' > ', ' ', $brands[$incoming->brand_id]) .' '. $name_p;

                    $product =
            
                    '{
                        "data": {
                            "id": "'. $incoming->id .'",
                            "type": "products",
                            "attributes": {
                                "code": "'. $incoming->code .'",
                                "name": "'. $name_p .'",
                                "vat_rate": '. $incoming->tax .',
                                "unit": "Adet",
                                "list_price": '. $price_p .',
                                "inventory_tracking": true
                            }
                        }
                    }';

                    // "initial_stock_count": '. $incoming->stock .'

                    if ($this->mode != 'test') :

                        if ($id)
                        {
                            $result = $incoming->match ? 

                                Controller::parasut('product', 'update', $product, $incoming->match) :

                                Controller::parasut('product', 'create', $product);

                            if (isset($result['data']['id']))
                            {
                                if (!$incoming->match)
                                {
                                    $incoming->match = $result['data']['id'];

                                    $incoming->save();
                                }

                                DB::commit();
                            }
                            
                            else
                            {
                                DB::rollBack();

                                return [false, 'Paraşüt\'e aktarılamadı. Tekrar deneyin.'];
                            }
                        }

                        else
                        {
                            $result = Controller::parasut('product', 'create', $product);

                            if (isset($result['data']['id']))
                            {
                                $incoming->match = $result['data']['id'];

                                $incoming->save();
                            }

                            else
                            {
                                $incoming->delete();

                                return [false, 'Paraşüt\'e aktarılamadı. Tekrar deneyin.'];
                            }
                        }

                    else: DB::commit();

                    endif;
                }
                
                catch (\Exception $e)
                {
                    $response = json_decode($e->getMessage());

                    if (!$id)

                        $incoming->delete();

                    else DB::rollBack();

                    return [false, '<b>Paraşüt</b>: '. $e->getMessage()];
                }

                foreach ($photos as $photo)
                {
                    $photo->delete();

                    File::delete('assets/images/products/'. $photo->name);
                }

                if ($r->has('photo'))
                {
                    foreach ($r->get('photo') as $i => $name)
                    {
                        File::move('assets/images/uploads/'. $name, 'assets/images/products/'. $name);

                        Photo::create(['product_id' => $incoming->id, 'name' => $name]);
                    }
                }

                if ($r->has('profile-select'))

                    Photo::where('name', $r->get('profile-select'))->update(['profile' => 1]);
                
                DB::table('product_a')
                    ->where('product_id', $incoming->id)
                    ->delete();

                if ($r->has('attribute'))
                {
                    foreach ($r->get('attribute') as $id => $value)
                    {
                        if ($value)
                        {
                            DB::table('product_a')->insert(
                            [
                                'product_id' => $incoming->id,
                                'attribute_id' => $id,
                                'value' => trim($value),
                            ]);
                        }
                    }
                }

                $this->commerce_put($incoming);

                $message = '';

                if ($this->mode != 'test')
                {
                    $result = self::merchant_put($incoming);

                    if (!$result)

                        $message = ' Fakat Merchant Center a aktarılamadı.';
                }

                return [true, 'Ürün kaydedildi.'. $message, '/urun/'. $incoming->id];
            }
            
            else if ($r->get('task') == 'sil')
            {
                /* $photos = Photo::where('product_id', $incoming->id)->where('profile', 0)->orderBy('order')->get();

                if (count($photos) > 0)
                {
                    foreach ($photos as $photo)
                    {
                        File::delete('assets/images/products/'. $photo->name);

                        $photo->delete();
                    }
                }

                $incoming->delete(); */

                try
                {
                    DB::beginTransaction();

                    DB::connection('commerce')->beginTransaction();

                    DB::connection('commerce')->table('urun')->where('id', $incoming->id)->update(['sil' => 1]);

                    $incoming->del = 1;

                    $incoming->save();
                }

                catch (\Exception $e)
                {
                    return [false, $e->getMessage()];
                }

                try
                {
                    $product =
            
                    '{
                        "data": {
                            "id": "'. $incoming->id .'",
                            "type": "products",
                            "attributes": {
                                "archived": true
                            }
                        }
                    }';

                    Controller::parasut('product', 'update', $product, $incoming->match);

                    DB::commit();

                    DB::connection('commerce')->commit();
                }

                catch (\Exception $e)
                {
                    $response = json_decode($e->getMessage());

                    DB::rollBack();

                    DB::connection('commerce')->rollBack();

                    return [false, '<b>Paraşüt</b>: '. $response->errors[0]->detail];
                }

                return [true, 'Ürün silindi.', '/urun'];
            }
        }

        else
        {
            $data['datas'] = Product::where('del', 0)->orderBy('name')->get();

            $data['categories'] = $this->category_list();

            $data['bullets'] = $incoming->bullet ? json_decode($incoming->bullet) : [null];

            $where = 'del = 0 AND (category_id IS NULL';

            if ($incoming->category_id)

                $where .= ' OR category_id = '. $incoming->category_id;

            $data['attributes'] = Attribute::whereRaw($where .')')->orderBy('name')->get();

            foreach ($photos as $photo)

                File::copy('assets/images/products/'. $photo->name, 'assets/images/uploads/'. $photo->name);
            
            return view('product.system-e', $data);
        }
    }

    public function commerce_put($incoming)
    {
        $temp = DB::table('product AS p')
            ->leftJoin('brand AS b', 'b.id', '=', 'p.brand_id')
            ->leftJoin('category AS c', function($join)
            {
                $join->on('c.id', '=', 'p.category_id')
                    ->where('c.del', 0);
            })
            ->select('p.*', 'b.name AS brand', 'c.id AS cat_id')
            ->where('p.id', $incoming->id)
            ->first();

        if ($temp->brand_id)
        {
            $brands = [];

            $root = Brand::find($temp->brand_id);

            while (1)
            {
                $brands[] = $root->name;

                if (!$root->root_id) 

                    break;

                else $root = Brand::find($root->root_id);
            }

            $brands = array_reverse($brands);

            $temp->brand = implode(' ', $brands);
        }

        $names = array_filter([$temp->brand, $temp->name, $temp->code, $temp->model_code]);

        $temp->name = implode(' ', array_unique($names));

        if ($temp->discount >= $temp->price)

            $temp->discount = null;

        $category_id_google = null;

        if ($temp->category_id)
        {
            $category = \App\Models\Category::find($temp->category_id);

            $category_match = json_decode($category->match);

            if (isset($category_match->google) && $category_match->google)

                $temp->category_id_google = $category_match->google;
        }

        $data = new \stdClass();

        $columns = 
        [
            'id' => 'id',
            'code' => 'kod',
            'model_code' => 'model_kod',
            'name' => 'isim',
            'name_invoice' => 'isim_resmi',
            'price' => 'fiyat',
            'discount' => 'indirim',
            'stock' => 'stok',
            'pre_description' => 'on_aciklama',
            'description' => 'aciklama',
            'brand' => 'marka',
            'category_id' => 'kat_id',
            'category_id_other' => 'kat_id_diger',
            'keyword' => 'keyword',
            'video' => 'video',
            'del' => 'sil',
        ];

        foreach ($columns as $key => $column)
        
            $data->$column = $temp->$key;

        $data->kat_id_google = $category_id_google;

        $cat_id = $data->kat_id;

        $temps_b = DB::table('product_a')
            ->where('product_a.product_id', $temp->id)
            ->join('attribute', 'attribute.id', '=', 'product_a.attribute_id')
            ->select('attribute.name', 'product_a.value')
            ->get();

        if (count($temps_b) > 0)
        {
            $data_b = $temp->bullet ? json_decode($temp->bullet, true) : [];

            foreach ($temps_b as $temp_b)
            
                $data_b[] = $temp_b->name .': '. $temp_b->value;

            $data->ozellik = json_encode($data_b);
        }

        $data->url = self::url($data, 'urun', 'commerce');

        $temp = DB::connection('commerce')->table('urun')->where('id', $data->id)->first();

        if ($temp)

            DB::connection('commerce')->table('urun')->where('id', $data->id)->update((array) $data);

        else DB::connection('commerce')->table('urun')->insert((array) $data);

        DB::connection('commerce')->table('foto')->where('urun_id', $data->id)->delete();

        $temps = DB::table('photo')->where('product_id', $data->id)->get();

        foreach ($temps as $temp)
        {
            $data = new \stdClass();

            $columns = 
            [
                'id' => 'id',
                'product_id' => 'urun_id',
                'name' => 'deger',
                'profile' => 'profil',
                'order' => 'sira',
            ];

            foreach ($columns as $key => $column)

                $data->$column = $temp->$key;

            $temp = DB::connection('commerce')->table('foto')->where('id', $temp->id)->first();

            if ($temp)

                DB::connection('commerce')->table('foto')->where('id', $temp->id)->update((array) $data);

            else DB::connection('commerce')->table('foto')->insert((array) $data);

            copy(public_path('assets/images/products/'. $data->deger), public_path('../../ticaret/public_html/img/'. $data->deger));
        }

        $temp = DB::connection('commerce')->table('kategori')->find($cat_id);

        if (!$temp)

            $this->commerce_put_cat(Category::find($cat_id));            
    }

    public static function merchant_put($incoming)
    {
        $client = new \Google\Client();

        $client->setAuthConfig(base_path('../entegre/assets/content-api-key.json'));

        $client->addScope(\Google_Service_ShoppingContent::CONTENT);

        $service = new \Google_Service_ShoppingContent($client);

        $product = new \Google_Service_ShoppingContent_Product();

        $data = DB::connection('commerce')
            ->table('urun')
            ->leftJoin('foto', function($join)
            {
                $join->on('foto.urun_id', '=', 'urun.id')
                    ->where('foto.profil', 1);
            })
            ->select('urun.*', 'foto.deger AS profil')
            ->where('urun.id', $incoming->id)
            ->first();

        if (!$data)

            exit;

        if (!$data->profil)

            exit;

        $product->setOfferId($data->kod);
        
        $product->setTitle($data->isim);
        
        $product->setDescription($data->on_aciklama);
        
        $product->setLink('https://noone.com.tr/urun/'. $data->url);

        $product->setMobileLink('https://noone.com.tr/urun/'. $data->url);

        $product->setAdsRedirect('https://noone.com.tr/urun/'. $data->url);
        
        $product->setImageLink('https://noone.com.tr/img/'. $data->profil);
        
        $photos = DB::connection('commerce')
            ->table('foto')
            ->where('urun_id', $data->id)
            ->where('profil', 0)
            ->orderBy('sira')
            ->get();

        if (count($photos) > 0)
        {
            $temp = [];

            foreach ($photos as $photo)

                $temp[] = 'https://noone.com.tr/img/'. $photo->deger;

            $product->setAdditionalImageLinks($temp);
        }

        $product->setContentLanguage('tr');
        
        $product->setTargetCountry('TR');
        
        $product->setChannel('online');
        
        $product->setAvailability($data->stok > 0 ? 'in stock' : 'out of stock');
        
        $product->setCondition('new');

        $price = new \Google_Service_ShoppingContent_Price();

        $price->setValue($data->fiyat);
        
        $price->setCurrency('TRY');

        $product->setPrice($price);

        if ($data->indirim)
        {
            $price = new \Google_Service_ShoppingContent_Price();

            $price->setValue($data->indirim);
            
            $price->setCurrency('TRY');

            $product->setSalePrice($price);
        }

        if ($data->kat_id)
        {
            $category = \App\Models\Category::find($data->kat_id);

            $category_match = json_decode($category->match);

            if (isset($category_match->google) && $category_match->google)

                $product->setGoogleProductCategory($category_match->google);

            $temp = [];

            while (1)
            {
                $temp[] = $category->name;

                if (!$category->root_id) 
            
                    break;
                
                else $category = \App\Models\Category::find($category->root_id);
            }

            $temp = array_reverse($temp);

            $product->setProductTypes(implode(' > ', $temp));
        }
        
        if ($data->marka)

            $product->setBrand($data->marka);
        
        $product->setIdentifierExists('yes');

        $product->setGtin($incoming->barcode);

        $temp = [];

        $attributes = DB::table('product_a')
            ->where('product_a.product_id', $data->id)
            ->join('attribute', 'attribute.id', '=', 'product_a.attribute_id')
            ->select('attribute.name', 'product_a.value')
            ->get();

        if (count($attributes) > 0)
        {
            $gender = ['Unisex' => 'unisex', 'Erkek' => 'male', 'Kız' => 'female'];

            $age = 
            [
                'Bebeğinizi Beklerken' => 'newborn',
                'Yenidoğan - 6 Aylık' => 'newborn',
                '6-12 Aylık' => 'infant',
                '12-18 Aylık' => 'toddler',
                '18-24 Aylık' => 'toddler',
                '2 Yaş ve Üzeri' => 'toddler',
                '3 Yaş ve Üzeri' => 'toddler',
                '4 Yaş ve Üzeri' => 'toddler',
                '5 Yaş ve Üzeri' => 'toddler',
                '6 Yaş ve Üzeri' => 'kids',
                '7 Yaş ve Üzeri' => 'kids',
                '8 Yaş ve Üzeri' => 'kids',
                '12 Yaş ve Üzeri' => 'kids'
            ];

            foreach ($attributes as $attribute)
            {
                $temp_a = new \Google_Service_ShoppingContent_ProductProductDetail();

                $temp_a->setAttributeName($attribute->name);

                $temp_a->setAttributeValue($attribute->value);

                $temp[] = $temp_a;

                if ($attribute->name == 'Cinsiyet' && isset($gender[$attribute->value]))

                    $product->setGender($gender[$attribute->value]);

                else if ($attribute->name == 'Yaş Grubu' && isset($age[$attribute->value]))

                    $product->setAgeGroup($age[$attribute->value]);

                else if ($attribute->name == 'Renk')

                    $product->setColor($attribute->value);
            }
        }

        if ($incoming->bullet)
        {
            foreach (json_decode($incoming->bullet, true) as $bullet)
            {
                $temp_a = new \Google_Service_ShoppingContent_ProductProductDetail();

                $temp_a->setAttributeName('Teknik');

                $temp_a->setAttributeValue($bullet);

                $temp[] = $temp_a;
            }
        }

        if (count($temp) > 0)

            $product->setProductDetails($temp);
        
        try
        {
            $service->products->insert(360703426, $product);

            return true;
        }

        catch (\Exception $e)
        {
            return false;
        }
    }

    public function price(Request $r)
    {
        $data['datas'] = Product::leftJoin('photo', function($join)
            {
                $join->on('photo.product_id', '=', 'product.id')
                    ->where('photo.profile', 1);
            })
            ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
            ->select('product.*', 'brand.name AS brand', 'photo.name AS profile')
            ->where('product.del', 0)
            ->orderBy('product.id', 'DESC')
            ->get();

        return view('product.price', $data);
    }

    public function photo(Request $r, $id)
    {
        foreach ($r->all() as $order => $name)
            
            Photo::where('name', $name)->update(['order' => $order]);
    }

    public function brand(Request $r, $id = null)
    {
        $incoming = $data['incoming'] = $id ? Brand::find($id) : new Brand;

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                foreach ($r->all() as $name => $value)
                {
                    if (in_array($name, ['task', '_token', 'photo', 'photo-del', 'match', 'match_name']))
                    
                        continue;

                    if ($r->has($name))
                    
                        $incoming->$name = $value;
                }

                if ($r->hasFile('photo'))
                {
                    $photo = Controller::file($r->file('photo'));
                    
                    if (!$photo[0]) 
                    
                        return [false, $photo[1]];
                }

                $incoming->match = json_encode($r->get('match'), JSON_NUMERIC_CHECK);

                $incoming->match_name = json_encode($r->get('match_name'));

                try 
                { 
                    $incoming->save();
                } 
                
                catch (\Exception $e) 
                { 
                    return [false, $e->getMessage()];
                }

                if ($r->hasFile('photo'))
                {
                    File::delete('assets/images/brands/'. $incoming->photo);

                    $incoming->photo = mt_rand() .'.'. $r->file('photo')->getClientOriginalExtension();

                    $r->file('photo')->move('assets/images/brands/', $incoming->photo);

                    $incoming->save();
                }

                if ($r->has('photo-del'))
                {
                    File::delete('assets/images/brands/'. $r->get('photo-del'));

                    $incoming->photo = null;

                    $incoming->save();
                }

                return [true, 'Marka kaydedildi.', '/urun/marka/'. $id];
            }

            else if ($r->get('task') == 'sil')
            {
                $GLOBALS['brands'] = null;

                $datas = $this->brand_list($incoming->id);
                
                $datas[$incoming->id] = $incoming->name;

                foreach ($datas as $id => $name)
                {
                    Brand::find($id)->update(['del' => 1]);

                    Product::where('brand_id', $id)->update(['brand_id' => null]);
                }

                return [true, 'Marka silindi.', '/urun/marka'];
            }
        }

        else
        {
            $data['datas'] = $this->brand_list();

            $data['temps'] = [];

            foreach (\App\Models\Data::orderBy('name')->get() as $temp)

                $data['temps'][$temp->name] = $temp->active;

            if ($id)
            {
                $data['match'] = json_decode($incoming->match, true);

                $data['match_name'] = json_decode($incoming->match_name, true);
            }

            else
            {  
                $data['match'] = $data['match_name'] = [];

                foreach ($data['temps'] as $name => $temp)
                
                    $data['match'][$name] = $data['match_name'][$name] = null;
            }

            $data['brands'] = ['trendyol' => []];

            return view('product.brand', $data);
        }
    }

    public function brand_list($root_id = null, $name = null, $reset = false)
    {  
        global $brands; 
        
        if ($reset) 
        
            $brands = [];

        foreach (Brand::where('root_id', $root_id)->where('del', 0)->orderBy('name')->get() as $brand)
        {
            $brands[$brand->id] = $name ? $name .' > '. $brand->name : $brand->name;

            $this->brand_list($brand->id, $brands[$brand->id]);
        }

        return $brands;
    }

    public function category(Request $r, $id = null)
    {
        $incoming = $data['incoming'] = $id ? Category::find($id) : new Category;

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $columns = ['root_id', 'name'];

                foreach ($columns as $column) 
                
                    $incoming->$column = $r->get($column);

                $incoming->commision = json_encode($r->get('commision'), JSON_NUMERIC_CHECK);

                $incoming->match = json_encode($r->get('match'), JSON_NUMERIC_CHECK);

                $incoming->root = $r->has('root') ? 1 : 0;
                    
                try 
                { 
                    $incoming->save();

                    $this->commerce_put_cat($incoming);
                } 
                
                catch (\Exception $e) 
                { 
                    return [false, $e->getMessage()];
                }

                return [true, 'Kategori kaydedildi.', '/urun/kategori/'. $id];
            }

            else if ($r->get('task') == 'sil')
            {
                $GLOBALS['categories'] = null;

                $datas = $this->category_list($incoming->id);
                
                $datas[$incoming->id] = $incoming->name;

                foreach ($datas as $id => $name)
                {
                    Category::find($id)->update(['del' => 1]);

                    DB::connection('commerce')->table('kategori')->where('id', $id)->update(['sil' => 1]);

                    Product::where('category_id', $id)->update(['del' => 1]);

                    DB::connection('commerce')->table('urun')->where('kat_id', $id)->update(['sil' => 1]);
                }

                return [true, 'Kategori silindi.', '/urun/kategori'];
            }
        }

        else
        {
            $data['datas'] = $this->category_list();

            $data['temps'] = [];

            foreach (\App\Models\Data::orderBy('name')->get() as $temp)

                $data['temps'][$temp->name] = $temp->active;

            if ($id)
            {
                $data['commision'] = json_decode($incoming->commision);

                $data['match'] = json_decode($incoming->match);

                if (!isset($data['match']->google))

                    $data['match']->google = 2847;
            }

            else
            {  
                $data['commision'] = $data['match'] = [];

                foreach ($data['temps'] as $name => $temp)
                {
                    $data['commision'][$name] = 0;

                    $data['match'][$name] = null;
                }

                $data['match']['google'] = 2847;
            }

            $categories_google = [];

            $categories = file_get_contents('https://www.google.com/basepages/producttype/taxonomy-with-ids.tr-TR.txt');

            foreach (explode("\n", $categories) as $i => $category)
            {
                if ($i == 0)

                    continue;

                try
                {
                    list($id, $name) = explode(' - ', $category);

                    $categories_google[] = (object) ['id' => $id, 'name' => $name];
                }

                catch (\Exception $e)
                {
                    continue;
                }
            }

            $data['categories'] = 
            [
                'trendyol' => $this->category_trendyol(true),
                'n11' => $this->category_n11(true),
                'gittigidiyor' => $this->category_g_g(true),
                'ciceksepeti' => $this->category_c_s(true),
                'google' => $categories_google,
            ];

            return view('product.category', $data);
        }
    }

    public function commerce_put_cat($incoming)
    {
        if (!$incoming)

            return false;

        $data = new \stdClass();

        $columns = 
        [
            'id' => 'id',
            'root_id' => 'bagli_id',
            'name' => 'isim',
        ];

        foreach ($columns as $key => $column)

            $data->$column = $incoming->$key;

        $control = Category::find($incoming->root_id);

        if (!$control) 

            $data->bagli_id = null;

        else if ($control->del) 

            $data->bagli_id = null;

        $data->url = self::url($data, 'kategori', 'commerce');

        $temp = DB::connection('commerce')->table('kategori')->where('id', $incoming->id)->first();

        if ($temp)

            DB::connection('commerce')->table('kategori')->where('id', $temp->id)->update((array) $data);

        else DB::connection('commerce')->table('kategori')->insert((array) $data);
    }

    public function category_list($root_id = null, $name = null, $reset = false)
    {  
        global $categories; 
        
        if ($reset) 
        
            $categories = [];

        foreach (Category::where('root_id', $root_id)->where('del', 0)->orderBy('name')->get() as $category)
        {
            $categories[$category->id] = $name ? $name .' > '. $category->name : $category->name;

            $this->category_list($category->id, $categories[$category->id]);
        }

        return $categories;
    }

    public function attribute(Request $r, $id = null)
    {
        $incoming = $data['incoming'] = $id ? Attribute::find($id) : new Attribute;

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                foreach (['category_id', 'name', 'option'] as $column)

                    $incoming->$column = $r->has($column) ? $r->get($column) : null;

                $incoming->require = $r->has('require') ? 1 : 0;

                try 
                { 
                    $incoming->save();
                } 
                
                catch (\Exception $e) 
                { 
                    return [false, $e->getMessage()];
                }

                return [true, 'Özellik kaydedildi.', '/urun/ozellik/'. $id];
            }

            else if ($r->get('task') == 'sil')
            {
                $incoming->del = 1;

                $incoming->save();

                return [true, 'Özellik silindi.', '/urun/ozellik'];
            }
        }
        
        else
        {
            $data['categories'] = $this->category_list();

            $data['datas'] = Attribute::where('del', 0)->orderBy('name')->get();

            return view('product.attribute', $data);
        }
    }

    public function list(Request $r)
    {
        $data['datas'] = Product::with(['brand', 'category', 'profile'])->where('del', 0)->orderBy('id', 'DESC')->get();

        $data['categories'] = $this->category_list();

        $data['brands'] = $this->brand_list();

        return view('product.system', $data);
    }

    // live

    public function live(Request $r)
    {
        if ($r->get('type') == 'system')
        {
            if (in_array($r->get('task'), ['price', 'discount', 'stock']))
            {
                try
                {
                    $column = $r->get('task');

                    DB::beginTransaction();

                    DB::connection('commerce')->beginTransaction();

                    $incoming = Product::find($r->get('code'));

                    $incoming->$column = $r->get('value');

                    $incoming->save();

                    $column_c = ['price' => 'fiyat', 'discount' => 'indirim', 'stock' => 'stok'];

                    if ($incoming->discount >= $incoming->price)

                        $incoming->discount = null;

                    $update = [];

                    foreach ($column_c as $local => $commerce)

                        $update[$commerce] = $incoming->$local;

                    DB::connection('commerce')->table('urun')->where('id', $incoming->id)->update($update);

                    if ($column != 'discount') :

                        try
                        {
                            $price_p = $incoming->price;

                            if ($incoming->tax > 0)

                                $price_p = $price_p / (1 + ($incoming->tax / 100));

                            $name_p = $incoming->name .' '. $incoming->code;

                            $brands = $this->brand_list();

                            if (isset($brands[$incoming->brand_id]))

                                $name_p = str_replace(' > ', ' ', $brands[$incoming->brand_id]) .' '. $name_p;

                            $product =
                    
                            '{
                                "data": {
                                    "id": "'. $incoming->id .'",
                                    "type": "products",
                                    "attributes": {
                                        "code": "'. $incoming->code .'",
                                        "name": "'. $name_p .'",
                                        "vat_rate": '. $incoming->tax .',
                                        "unit": "Adet",
                                        "list_price": '. $price_p .',
                                        "inventory_tracking": true
                                    }
                                }
                            }';

                            // "initial_stock_count": '. $incoming->stock .'

                            $result = Controller::parasut('product', 'update', $product, $incoming->match);

                            if (isset($result['data']['id']))
                            {
                                DB::commit();

                                DB::connection('commerce')->commit();
                            }

                            else
                            {
                                DB::rollBack();

                                DB::connection('commerce')->rollBack();

                                return false;
                            }
                        }
                        
                        catch (\Exception $e)
                        {
                            DB::rollBack();

                            DB::connection('commerce')->rollBack();

                            echo $e->getMessage();

                            return false;
                        }

                    endif;

                    try
                    {
                        self::merchant_put($incoming);
                    }

                    catch (\Exception $e)
                    {
                        DB::rollBack();

                        DB::connection('commerce')->rollBack();

                        echo $e->getMessage();

                        return false;
                    }

                    DB::commit();

                    DB::connection('commerce')->commit();

                    return true;
                }

                catch(\Exception $e)
                {
                    echo $e->getMessage();

                    return false;
                }
            }

            else if (in_array($r->get('task'), ['price_t', 'discount_t']))
            {
                try
                {
                    $column = $r->get('task');

                    DB::beginTransaction();

                    $incoming = Product::find($r->get('code'));

                    $incoming->$column = $r->get('value');

                    $incoming->save();

                    if ($incoming->discount_t > $incoming->price_t)
                    {
                        DB::rollBack();

                        return false;
                    }

                    $datas = Match::where('product_id', $incoming->id)
                        ->where('type', 'trendyol')
                        ->get();

                    if (count($datas) > 0)
                    {
                        $query = [];

                        $column_t = ['price_t' => 'listPrice', 'discount_t' => 'salePrice'];

                        foreach ($datas as $i => $data)
                        {
                            $query[$i] =
                            [
                                'barcode' => $data->code,
                                $column_t[$column] => $incoming->$column
                            ];
                        }

                        $result = self::trendyol('POST', 'products/price-and-inventory', 
                        [
                            'items' => $query
                        ]);

                        if (!isset($result->batchRequestId))
                        {
                            DB::rollBack();

                            return false;
                        }

                        else
                        {
                            DB::commit();

                            return true;
                        }
                    }

                    else
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                catch(\Exception $e)
                {
                    return false;
                }
            }

            else if (in_array($r->get('task'), ['start', 'stop']))
            {
                DB::beginTransaction();

                $incoming = Product::find($r->get('id'));

                $incoming->stop = $r->get('task') == 'start' ? 0 : 1;

                $incoming->save();

                $datas = Match::where('product_id', $incoming->id)
                        ->where('type', 'trendyol')
                        ->get();

                if (count($datas) > 0)
                {
                    $query = [];

                    foreach ($datas as $i => $data)
                    {
                        $query[$i] =
                        [
                            'barcode' => $data->code,
                            'quantity' => $incoming->stop ? 0 : $incoming->stock,
                        ];
                    }

                    $result = self::trendyol('POST', 'products/price-and-inventory', 
                    [
                        'items' => $query
                    ]);

                    if (!isset($result->batchRequestId))
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                DB::commit();

                return true;
            }

            else if (in_array($r->get('task'), ['price_n', 'discount_n']))
            {
                try
                {
                    $column = $r->get('task');

                    DB::beginTransaction();

                    $incoming = Product::find($r->get('code'));

                    $incoming->$column = $r->get('value');

                    $incoming->save();

                    if ($incoming->discount_n >= $incoming->price_n)
                    {
                        DB::rollBack();

                        return false;
                    }

                    $datas = Match::where('product_id', $incoming->id)
                        ->where('type', 'n11')
                        ->get();

                    if (count($datas) > 0)
                    {
                        $control = [];

                        foreach ($datas as $i => $data)
                        {
                            if ($column == 'discount_n')
                            {
                                $result = self::n11('ProductService', 'UpdateDiscountValueBySellerCode', 
                                [
                                    'productSellerCode' => $data->code,
                                    'productDiscount' =>
                                    [
                                        'discountType' => 3,
                                        'discountValue' => $incoming->discount_n,
                                        'discountStartDate' => null,
                                        'discountEndDate' => null,
                                    ],
                                ]);
                            }

                            else if ($column == 'price_n')
                            {
                                $result = Controller::n11('ProductService', 'UpdateProductPriceBySellerCode',
                                [
                                    'productSellerCode' => $data->code,
                                    'price' => $incoming->price_n,
                                    'currencyType' => 1,
                                    'stockItems' => null,
                                ]);
                            }

                            if (isset($result->result->status))
                            {
                                if ($result->result->status != 'success')

                                    $control[] = $data->code;                                    
                            }

                            else $control[] = $data->code;
                        }

                        // echo count($control) .' '. count($datas);

                        if (count($control) == count($datas))
                        {
                            DB::rollBack();

                            return false;
                        }

                        else
                        {
                            DB::commit();

                            return true;
                        }
                    }

                    else
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                catch(\Exception $e)
                {
                    return false;
                }
            }

            else if (in_array($r->get('task'), ['start_n', 'stop_n']))
            {
                DB::beginTransaction();

                $incoming = Product::find($r->get('id'));

                $incoming->stop_n = $r->get('task') == 'start_n' ? 0 : 1;

                $incoming->save();

                $datas = Match::where('product_id', $incoming->id)
                    ->where('type', 'n11')
                    ->get();

                if (count($datas) > 0)
                {
                    $control = [];

                    foreach ($datas as $i => $data)
                    {
                        $result = Controller::n11('ProductStockService', 'UpdateStockByStockSellerCode',
                        [
                            'stockItems' =>
                            [
                                'stockItem' =>
                                [
                                    'sellerStockCode' => $data->code,
                                    'quantity' => $incoming->stop_n ? 0 : $incoming->stock,
                                    'version' => null      
                                ]
                            ]
                        ]);

                        if (isset($result->result->status))
                        {
                            if ($result->result->status != 'success')

                                $control[] = $data->code;                                    
                        }

                        else $control[] = $data->code;
                    }

                    if (count($control) == count($datas))
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                DB::commit();

                return true;
            }

            else if (in_array($r->get('task'), ['price_c', 'discount_c']))
            {
                try
                {
                    $column = $r->get('task');

                    DB::beginTransaction();

                    $incoming = Product::find($r->get('code'));

                    $incoming->$column = $r->get('value');

                    $incoming->save();

                    if ($incoming->discount_c > $incoming->price_c)
                    {
                        DB::rollBack();

                        return false;
                    }

                    $datas = Match::where('product_id', $incoming->id)
                        ->where('type', 'ciceksepeti')
                        ->get();

                    if (count($datas) > 0)
                    {
                        $query = [];

                        $column_t = ['price_c' => 'listPrice', 'discount_c' => 'salesPrice', 'stock' => 'stockQuantity'];

                        foreach ($datas as $i => $data)
                        {
                            $query[$i] = ['stockCode' => $data->code];

                            foreach ($column_t as $name => $name_c)

                                $query[$i][$name_c] = $incoming->$name;
                        }

                        $result = self::c_s('PUT', 'Products/price-and-stock',
                        [
                            'items' => $query
                        ]);

                        if (!isset($result->batchId))
                        {
                            DB::rollBack();

                            return false;
                        }

                        else
                        {
                            DB::commit();

                            return true;
                        }
                    }

                    else
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                catch(\Exception $e)
                {
                    return false;
                }
            }

            else if (in_array($r->get('task'), ['start_c', 'stop_c']))
            {
                DB::beginTransaction();

                $incoming = Product::find($r->get('id'));

                $incoming->stop_c = $r->get('task') == 'start_c' ? 0 : 1;

                $incoming->save();

                $datas = Match::where('product_id', $incoming->id)
                    ->where('type', 'ciceksepeti')
                    ->get();

                if (count($datas) > 0)
                {
                    $query = [];

                    foreach ($datas as $i => $data)
                    {
                        $query[$i] =
                        [
                            'stockCode' => $data->code,
                            'stockQuantity' => $incoming->stop ? 0 : $incoming->stock,
                        ];
                    }

                    $result = self::c_s('PUT', 'Products/price-and-stock',
                    [
                        'items' => $query
                    ]);

                    if (!isset($result->batchId))
                    {
                        DB::rollBack();

                        return false;
                    }
                }

                DB::commit();

                return true;
            }

            else if ($r->get('task') == 'attribute')
            {
                $where = 'del = 0 AND (category_id IS NULL';

                if ($r->get('id'))

                    $where .= ' OR category_id = '. $r->get('id');

                $attributes = Attribute::whereRaw($where .')')->orderBy('name')->get();

                return view('product.attribute-l', 
                [
                    'type' => 'system', 
                    'attributes' => $attributes
                ]);
            }
        }

        else if ($r->get('type') == 'trendyol')
        {
            if ($r->get('task') == 'match')
            {
                $incoming = Match::where('type', $r->get('type'))
                    ->where('code', $r->get('code'))
                    ->first();

                if (!$incoming)

                    $incoming = new Match;

                else if (!$r->get('value'))
                {
                    $incoming->delete();

                    return true;
                }

                $incoming->type = $r->get('type');

                $incoming->product_id = $r->get('value');

                $incoming->code = $r->get('code');

                $incoming->save();

                return true;
            }

            else if ($r->get('task') == 'quantity' || 
                $r->get('task') == 'listPrice' || 
                $r->get('task') == 'salePrice')
            {
                $result = Controller::trendyol('POST', 'products/price-and-inventory', 
                [
                    'items' =>
                    [
                        [
                            "barcode" => $r->get('code'),
                            $r->get('task') => $r->get('value')
                        ],
                    ],
                ]);

                return isset($result->batchRequestId) ? true : false;
            }

            else if ($r->get('task') == 'brand')
            {
                $results = Controller::trendyol('GET', 'brands/by-name', ['name' => $r->get('name')], false);

                return isset($results->error) ? false : $results;
            }

            else if ($r->get('task') == 'attribute')
            {
                $url = 'product-categories/'. $r->get('id') .'/attributes';

                $results = Controller::trendyol('GET', $url, null, false);

                if (isset($results->categoryAttributes))
                {
                    return view('product.attribute-l', 
                    [
                        'type' => 'trendyol', 
                        'attributes' => $results->categoryAttributes
                    ]);
                }

                else return false;
            }
        }

        else if ($r->get('type') == 'n11')
        {
            if ($r->get('task') == 'category')
            {
                $results = Controller::n11('CategoryService', 'GetSubCategories', 
                [
                    'categoryId' => $r->get('id')
                ]);

                return isset($results->category->subCategoryList->subCategory) ? 
                
                    $results->category->subCategoryList->subCategory :

                    false;
            }

            else if ($r->get('task') == 'category-parent')
            {
                $result = Controller::n11('CategoryService', 'GetParentCategory', 
                [
                    'categoryId' => $r->get('id')
                ]);

                $categories = [];

                while (isset($result->category->parentCategory))
                {
                    $categories[] = $result->category->parentCategory->id;

                    $result = Controller::n11('CategoryService', 'GetParentCategory', 
                    [
                        'categoryId' => $result->category->parentCategory->id
                    ]);
                }

                return count($categories) > 0 ? array_reverse($categories) : false;
            }

            else if ($r->get('task') == 'displayPrice')
            {
                $result = self::n11('ProductService', 'UpdateDiscountValueBySellerCode', 
                [
                    'productSellerCode' => $r->get('id'),
                    'productDiscount' =>
                    [
                        'discountType' => 3,
                        'discountValue' => $r->get('value'),
                        'discountStartDate' => null,
                        'discountEndDate' => null,
                    ],
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'price')
            {
                $result = Controller::n11('ProductService', 'UpdateProductPriceBySellerCode',
                [
                    'productSellerCode' => $r->get('id'),
                    'price' => $r->get('value'),
                    'currencyType' => $r->get('currency'),
                    'stockItems' => null,
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'quantity')
            {
                $result = Controller::n11('ProductStockService', 'UpdateStockByStockSellerCode',
                [
                    'stockItems' =>
                    [
                        'stockItem' =>
                        [
                            'sellerStockCode' => $r->get('id'),
                            'quantity' => $r->get('value'),
                            'version' => null      
                        ]
                    ]
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }

            else if ($r->get('task') == 'match')
            {
                $incoming = Match::where('type', $r->get('type'))
                    ->where('code', $r->get('id'))
                    ->first();

                if (!$incoming)

                    $incoming = new Match;

                else if (!$r->get('value'))
                {
                    $incoming->delete();

                    return true;
                }

                $incoming->type = $r->get('type');

                $incoming->product_id = $r->get('value');

                $incoming->code = $r->get('id');

                $incoming->save();

                return true;
            }

            else if ($r->get('task') == 'attribute')
            {
                $result = Controller::n11('CategoryService', 'GetCategoryAttributes',
                [
                    'categoryId' => $r->get('id')
                ]);

                if (isset($result->category->attributeList->attribute))
                {
                    $data = 
                    [
                        'type' => 'n11', 
                        'attributes' => $result->category->attributeList->attribute
                    ];

                    if (!is_array($data['attributes']))

                        $data['attributes'] = [$data['attributes']];

                    $result = Controller::n11('ProductService', 'GetProductByProductId',
                    [
                        'productId' => $r->get('incoming'),
                    ]);

                    if (isset($result->product))
                    
                        $data['incoming'] = $result->product;

                    return view('product.attribute-l', $data)->render();
                }

                else return false;
            }

            else if ($r->get('task') == 'stock')
            
                return view('product.stock', ['i' => $r->get('length')])->render();

            else if ($r->get('task') == 'StartSellingProductBySellerCode'
                || $r->get('task') == 'StopSellingProductBySellerCode')
            {
                $result = Controller::n11('ProductSellingService', $r->get('task'),
                [
                    'productSellerCode' => $r->get('id')
                ]);

                return $result->result->status == 'success' ? true : $result->result->errorMessage;
            }
        }

        else if ($r->get('type') == 'gittigidiyor')
        {
            if ($r->get('task') == 'category')
            {
                $results = Controller::g_g('CategoryService', 'getSubCategories', 
                [
                    'categoryCode' => $r->get('id'),
                    'withSpecs' => false,
                    'withDeepest' => false,
                    'withCatalog' => false,
                ]);

                return isset($results->categories->category) ? 
                
                    $results->categories->category :

                    false;
            }

            else if ($r->get('task') == 'marketPrice' 
                || $r->get('task') == 'price'
                || $r->get('task') == 'stock')
            {
                $function = 'update'. ucfirst($r->get('task'));

                $param = ['productId' => $r->get('id')];

                if ($r->get('task') != 'marketPrice')
                
                    $param['itemId'] = $r->get('item_id');

                $param[$r->get('task')] = $r->get('value');

                if ($r->get('task') != 'marketPrice')

                    $param['cancelBid'] = $r->get('task') == 'buyNowPrice' ? true : false;

                $result = Controller::g_g('IndividualProductService', $function, $param);

                return $result->ackCode == 'success' ? true : $result->error->message;
            }

            else if ($r->get('task') == 'match')
            {
                $incoming = Match::where('type', $r->get('type'))
                    ->where('code', $r->get('id'))
                    ->first();

                if (!$incoming)

                    $incoming = new Match;

                else if (!$r->get('value'))
                {
                    $incoming->delete();

                    return true;
                }

                $incoming->type = $r->get('type');

                $incoming->product_id = $r->get('value');

                $incoming->code = $r->get('id');

                $incoming->save();

                return true;
            }

            else if ($r->get('task') == 'finishEarly'
                || $r->get('task') == 'calculatePriceForShoppingCart')
            {
                $result = Controller::g_g('IndividualProductService', $r->get('task'),
                [
                    'productIdList' => [$r->get('id')],
                    'itemIdList' => null,
                ]);

                return $result->ackCode == 'success' ? true : $result->error->message;
            }

            else if ($r->get('task') == 'updateStockAndActivateProduct')
            {
                $result = Controller::g_g('IndividualProductService', $r->get('task'),
                [
                    'productId' => $r->get('id'),
                    'itemId' => $r->get('item_id'),
                    'stock' => $r->get('stock')
                ]);

                return $result->ackCode == 'success' ? true : $result->error->message;
            }

            else if ($r->get('task') == 'attribute')
            {
                $results = self::g_g('CategoryService', 'getCategorySpecs',
                [
                    'categoryCode' => $r->get('id')
                ]);

                if (isset($results->specs->spec))
                {
                    $attributes = $results->specs->spec;

                    if (!is_array($attributes)) 
                    
                        $attributes = [$attributes];

                    $data = 
                    [
                        'type' => 'gittigidiyor', 
                        'attributes' => $attributes
                    ];

                    $result = Controller::g_g('IndividualProductService', 'getProduct',
                    [
                        'productId' => $r->get('incoming')
                    ]);

                    if (isset($result->productDetail))
                    
                        $data['incoming'] = $result->productDetail;

                    return view('product.attribute-l', $data);
                }

                else return false;
            }

            else if ($r->get('task') == 'catalog-c')
            {
                $results = self::g_g('CategoryV2Service', 'getRequiredCategorySpecs',
                [
                    'categoryCode' => $r->get('id')
                ]);

                if (isset($results->catalogRequired) && $results->catalogRequired)
                
                    return $results->requiredSpecs->spec;

                else return false;
            }

            else if ($r->get('task') == 'catalog')
            {
                $results = self::g_g('CatalogV2Service', 'searchCatalog',
                [
                    'page' => 1,
                    'rowCount' => 50,
                    'criteria' =>
                    [
                        'keyword' => $r->get('name'),
                        'categoryCode' => $r->get('cat')
                    ]
                ]);

                return isset($results->catalogs->catalog) ? $results->catalogs->catalog : false;
            }
        }

        else if ($r->get('type') == 'ciceksepeti')
        {
            if ($r->get('task') == 'match')
            {
                $incoming = Match::where('type', $r->get('type'))
                    ->where('code', $r->get('code'))
                    ->first();

                if (!$incoming)

                    $incoming = new Match;

                else if (!$r->get('match'))
                {
                    $incoming->delete();

                    return true;
                }

                $incoming->type = $r->get('type');

                $incoming->product_id = $r->get('match');

                $incoming->code = $r->get('code');

                $incoming->save();

                return true;
            }

            else if ($r->get('task') == 'listPrice' || 
                $r->get('task') == 'salesPrice' || 
                $r->get('task') == 'stockQuantity')
            {
                $result = Controller::c_s('PUT', 'Products/price-and-stock', 
                [
                    'items' =>
                    [
                        [
                            "stockCode" => $r->get('code'),
                            "stockQuantity" => $r->get('stockQuantity'),
                            "listPrice" => $r->get('listPrice'),
                            "salesPrice" => $r->get('salesPrice'),
                        ],
                    ],
                ]);

                return isset($result->batchId) ? true : $result->message;
            }

            else if ($r->get('task') == 'attribute')
            {
                $results = Controller::c_s('GET', 'Categories/'. $r->get('id') .'/attributes');

                if (isset($results->categoryAttributes))
                {
                    return view('product.attribute-l', 
                    [
                        'type' => 'ciceksepeti', 
                        'attributes' => $results->categoryAttributes
                    ]);
                }

                else return false;
            }
        }

        else if ($r->get('type') == 'hepsiburada')
        {
            if ($r->get('task') == 'match')
            {
                $incoming = Match::where('type', $r->get('type'))
                    ->where('code', $r->get('code'))
                    ->first();

                if (!$incoming)

                    $incoming = new Match;

                else if (!$r->get('match'))
                {
                    $incoming->delete();

                    return true;
                }

                $incoming->type = $r->get('type');

                $incoming->product_id = $r->get('match');

                $incoming->code = $r->get('code');

                $incoming->save();

                return true;
            }

            else if ($r->get('task') == 'AvailableStock'
                || $r->get('task') == 'Price')
            {
                $param = 
                [
                    'root' => 'listings',
                    'data' =>
                    [
                        'listing' =>
                        [
                            'UniqueIdentifier' => null,
                            'HepsiburadaSku' => $r->get('code_h'),
                            'MerchantSku' => $r->get('code'),
                            'AvailableStock' => $r->get('AvailableStock'),
                            'Price' => $r->get('Price'),
                            'DispatchTime' => $r->get('dispatch'),
                        ]
                    ]
                ];

                $result = self::h_b('POST', ['listing', 'listings', 'inventory-uploads'], $param);

                return isset($result->Id) ? true : false;
            }

            else if ($r->get('task') == 'activate'
                || $r->get('task') == 'deactivate')
            {
                self::h_b('POST', ['listing', 'listings', 'sku/'. $r->get('id') .'/'. $r->get('task')]);

                return true;
            }
        }
    }
    
    // trendyol

    public function list_trendyol(Request $r)
    {
        $data['products'] = Product::has('brand')->where('del', 0)->orderBy('name')->get();

        $temp = \App\Models\Data::where('name', 'trendyol')->first();

        $temp = json_decode($temp->data);

        $show = $data['show'] = 200;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $param =
        [
            'supplierId' => $temp->supplier_id, 
            'size' => $show, 
            'page' => $page - 1
        ];

        if ($r->has('barcode'))

            $param['barcode'] = $r->get('barcode');

        $data['datas'] = Controller::trendyol('GET', 'products', $param);
        
        return view('product.trendyol', $data);
    }

    public function index_trendyol(Request $r, $barcode = null)
    {
        if ($barcode)
        {
            $result = Controller::trendyol('GET', 'products', ['barcode' => $barcode]);

            if (isset($result->content))

                $data['incoming'] = $result->content[0];
        }

        if (!isset($data['incoming']))
        {
            if ($r->has('s'))
            {
                $system = Product::where('product.id', $r->get('s'))
                    ->leftJoin('category', 'category.id', '=', 'product.category_id')
                    ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
                    ->selectRaw('IFNULL(JSON_EXTRACT(category.match, "$.trendyol"), "null") AS category_id_trendyol')
                    ->selectRaw('product.*, JSON_UNQUOTE(JSON_EXTRACT(brand.match_name, "$.trendyol")) AS brand_name')
                    ->selectRaw('IFNULL(JSON_EXTRACT(brand.match, "$.trendyol"), "null") AS brand_id_trendyol')
                    ->selectRaw('brand.name AS brand_system')
                    ->first();
            }

            else
            {
                $system = new Product;

                $system->category_id_trendyol = 'null';

                $system->brand_name = null;

                $system->brand_id_trendyol = 'null';
            }

            $photos = Photo::where('product_id', $system->id)->orderBy('profile', 'DESC')->orderBy('order')->get();

            if (count($photos) > 0)
            {
                $temp = [];

                foreach ($photos as $photo)

                    $temp[] = '{"url":"'. url('assets/images/products/'. $photo->name) .'"}';

                $temp = implode(',', $temp);
            }

            else $temp = null;

            $names = array_filter([$system->brand_system, $system->name, $system->code, $system->model_code]);

            $system->name = implode(' ', array_unique($names));

            $columns =

            '{
                "barcode": "'. $system->barcode .'",
                "title": "'. $system->name .'",
                "productMainId": "'. $system->model_code .'",
                "brand": "'. $system->brand_name .'",
                "brandId": '. $system->brand_id_trendyol .',
                "pimCategoryId": '. $system->category_id_trendyol .',
                "quantity": '. $system->stock .',
                "stockCode": "'. $system->code .'",
                "dimensionalWeight": '. $system->deci .',
                "description": "'. htmlentities($system->description) .'",
                "currencyType": "TRY",
                "listPrice": '. $system->price .',
                "salePrice": '. $system->discount .',
                "vatRate": '. $system->tax .',
                "cargoCompanyId": null,
                "images": ['. $temp .'],
                "attributes": [],
                "shipmentAddressId": null,
                "returningAddressId": null
            }';

            $data['incoming'] = json_decode(preg_replace('/[[:cntrl:]]/', '', $columns));
        }

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $temp = [];

                foreach ($r->all() as $name => $value)
                {
                    if (in_array($name, ['task', '_token', 'photo-add', 'photo-del']))

                        continue;

                    if (is_array($value))
                    {
                        if (count($value) > 0)

                            $temp[$name] = $value;
                    }

                    else if (!is_null($value))

                        $temp[$name] = $value;
                }
                
                if (isset($temp['attributes']))
                {
                    $attributes = [];

                    foreach ($temp['attributes'] as $attribute)
                    {
                        if (isset($attribute['attributeValueId']))
                        {
                            if (!is_null($attribute['attributeValueId']))

                                $attributes[] = $attribute;
                        }

                        else if (isset($attribute['customAttributeValue']))
                        {
                            if (!is_null($attribute['customAttributeValue']))

                                $attributes[] = $attribute;
                        }
                    }

                    $temp['attributes'] = $attributes;
                }

                if ($r->hasFile('photo-add'))
                {
                    if (!isset($temp['photos']))

                        $temp['photos'] = [];

                    foreach ($r->file('photo-add') as $photo)
                    {
                        $photo = Controller::file($photo);
                        
                        if (!$photo[0])

                            return [false, $photo[1]];
                    }

                    foreach ($r->file('photo-add') as $i => $photo)
                    {
                        $name = mt_rand() .'.'. $photo->getClientOriginalExtension();

                        $r->file('photo-add')[$i]->move('assets/images/products', $name);

                        $temp['photos'][] = url('assets/images/products/'. $name);
                    }
                }

                if (isset($temp['photos']))
                {
                    if ($r->has('photo-del'))
                    
                        $temp['photos'] = array_diff($temp['photos'], $r->get('photo-del'));

                    if (count($temp['photos']) > 0)
                    {
                        $photos = [];

                        foreach ($temp['photos'] as $photo)
                        
                            $photos[] = ['url' => $photo];

                        $temp['photos'] = $photos;
                    }

                    else unset($temp['photos']);
                }

                $temp['currencyType'] = 'TRY';

                $method = $barcode ? 'PUT' : 'POST';

                $result = Controller::trendyol($method, 'v2/products', ['items' => [$temp]]);

                return isset($result->errors) ? 
                
                    [false, $result->errors[0]->message] : 
                    
                    [true, 'Ürün kaydedildi.', '/urun/trendyol/'. $barcode];
            }
        }

        else
        {
            $data['categories'] = $this->category_trendyol();

            $data['cargos'] = Controller::trendyol('GET', 'shipment-providers', null, false);

            $data['addresses'] = Controller::trendyol('GET', 'addresses');

            // return var_dump($data['incoming']);

            if ($data['incoming']->pimCategoryId)
            {
                $url = 'product-categories/'. $data['incoming']->pimCategoryId .'/attributes';

                $results = Controller::trendyol('GET', $url, null, false);

                if (isset($results->categoryAttributes))
                
                    $data['attributes'] = $results->categoryAttributes;
            }

            return view('product.trendyol-e', $data);
        }
    }

    public function category_trendyol($object = false)
    {
        $results = Controller::trendyol('GET', 'product-categories', null, false);

        $results = json_encode($results);

        $results = json_decode($results, true);

        $temp = $level = [];

        $j = $k = 0;

        array_walk_recursive($results, function($a, $i) use (&$temp, &$j, &$k, &$level)
        {
            if ($i == 'id')
            
                $j = $a;

            else if ($i == 'name')
            
                $temp[$j] = $a;

            else if ($i == 'parentId')
            {
                $temp[$j] = $temp[$a] .' > '. $temp[$j];

                $level[$a] = $temp[$j];
            }
        });

        $categories = [];

        foreach ($temp as $id => $name)
        {
            if (!isset($level[$id]))
            {
                if ($object)

                    $categories[] = (object) ['id' => $id, 'name' => $name];

                else $categories[$id] = $name;
            }
        }

        return $categories;
    }

    // n11

    public function category_n11($object = false)
    {
        $results = Controller::n11('CategoryService', 'GetTopLevelCategories');

        $categories = [];

        if (isset($results->categoryList->category))
        {
            $categories = $results->categoryList->category;

            // usort($categories, fn($a, $b) => strcmp($a->name, $b->name));
        }

        if (!$object)
        {
            $temp = [];

            foreach ($categories as $category)
            
                $temp[$category->id] = $category->name;

            $categories = $temp;
        }

        return $categories;
    }

    public function list_n11(Request $r)
    {
        $data['products'] = Product::has('brand')->where('del', 0)->orderBy('name')->get();

        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $data['datas'] = Controller::n11('ProductService', 'GetProductList',
        [
            'pagingData' =>
            [
                'currentPage' => $page - 1,
                'pageSize' => $show
            ]
        ]);

        // dd($data['datas']); exit;

        return view('product.n11', $data);
    }

    public function index_n11(Request $r, $id = null)
    {
        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $xml = 
        
                '<product>
                    <productSellerCode></productSellerCode>
                    <maxPurchaseQuantity></maxPurchaseQuantity>
                    <title></title>
                    <subtitle></subtitle>
                    <description></description>
                    <category></category>
                    <specialProductInfoList>null</specialProductInfoList>
                    <price></price> 
                    <domestic>null</domestic>
                    <currencyType></currencyType>
                    <images></images>
                    <approvalStatus>null</approvalStatus>
                    <attributes></attributes>
                    <saleStartDate/>
                    <saleEndDate/>
                    <productionDate/>
                    <expirationDate/>
                    <productCondition>null</productCondition>
                    <preparingDay></preparingDay>
                    <domestic></domestic>
                    <discount></discount>
                    <shipmentTemplate></shipmentTemplate>
                    <stockItems>
                        <stockItem>
                            <bundle/>
                            <mpn/>
                            <gtin></gtin>
                            <oem></oem>
                            <n11CatalogId></n11CatalogId>
                            <quantity></quantity>
                            <sellerStockCode></sellerStockCode>
                            <attributes>
                                <attribute>
                                    <name></name>
                                    <value></value>
                                </attribute>
                            </attributes>
                            <optionPrice/>
                        </stockItem>
                    </stockItems>
                    <groupAttribute>null</groupAttribute>
                    <groupItemCode>null</groupItemCode>
                    <itemName>null</itemName>
                    <unitInfo>
                        <unitType>null</unitType>
                        <unitWeight>null</unitWeight>
                    </unitInfo>
                </product>';
                
                $xml = simplexml_load_string($xml); 
                
                $xml = json_encode($xml); 
                
                $xml = json_decode($xml, true);

                foreach ($r->all() as $name => $value)
                {
                    if (isset($xml[$name]))

                        $xml[$name] = $value;
                }

                $xml['domestic'] = $r->has('domestic') ? true : false;

                if ($r->hasFile('profile'))
                {
                    $photo = Controller::file($r->file('profile'));
                        
                    if (!$photo[0])

                        return [false, $photo[1]];

                    $name = mt_rand() .'.'. $photo[1]->getClientOriginalExtension();

                    $r->file('profile')->move('assets/images/products', $name);

                    $xml['images']['image'][] = 
                    [
                        'url' => url('assets/images/products/'. $name),
                        'order' => 1
                    ];

                    $j = 2;

                    foreach ($xml['images']['image'] as $i => $image)
                    {
                        if ($image['url'] == url('assets/images/products/'. $name))

                            continue;

                        $xml['images']['image'][$i]['order'] = $j;

                        $j++;
                    }
                }

                if ($r->has('photo-del'))
                {
                    foreach ($xml['images']['image'] as $i => $image)
                    {
                        if (in_array($image['url'], $r->get('photo-del')))

                            unset($xml['images']['image'][$i]);
                    }
                }

                if ($r->hasFile('photo-add'))
                {
                    foreach ($r->file('photo-add') as $photo)
                    {
                        $photo = Controller::file($photo);
                        
                        if (!$photo[0])

                            return [false, $photo[1]];
                    }

                    foreach ($r->file('photo-add') as $i => $photo)
                    {
                        $name = mt_rand() .'.'. $photo->getClientOriginalExtension();

                        $r->file('photo-add')[$i]->move('assets/images/products', $name);

                        $xml['images']['image'][] = 
                        [
                            'url' => url('assets/images/products/'. $name),
                            'order' => count($xml['images']['image']) + 1
                        ];
                    }
                }

                if (isset($xml['attributes']['attribute']))
                {
                    foreach ($xml['attributes']['attribute'] as $i => $attribute)
                    {
                        if ($attribute['custom'])

                            $xml['attributes']['attribute'][$i]['value'] = $attribute['custom'];

                        if (!$xml['attributes']['attribute'][$i]['value'])

                            unset($xml['attributes']['attribute'][$i]);

                        unset($xml['attributes']['attribute'][$i]['custom']);
                    }

                    sort($xml['attributes']['attribute']);
                }

                if (isset($xml['stockItems']['stockItem']))
                {
                    foreach ($xml['stockItems']['stockItem'] as $j => $item)
                    {
                        if (isset($item['attributes']['attribute']))
                        {
                            foreach ($item['attributes']['attribute'] as $i => $attribute)
                            {
                                if (isset($attribute['custom']) && $attribute['custom'])
                                {
                                    $item['attributes']['attribute'][$i]['value'] = $attribute['custom'];

                                    unset($item['attributes']['attribute'][$i]['custom']);
                                }

                                if (!$item['attributes']['attribute'][$i]['value'])

                                    unset($item['attributes']['attribute'][$i]);
                            }

                            sort($item['attributes']['attribute']);

                            $xml['stockItems']['stockItem'][$j] = $item;
                        }
                    }
                }

                // return [false, json_encode($xml)];

                if (isset($xml['discount']['value']) == $xml['price'])

                    $xml['discount'] = null;

                $xml = ['product' => $xml];

                $result = Controller::n11('ProductService', 'SaveProduct', $xml);

                if (isset($result->result->status))
                {
                    if ($result->result->status == 'failure')

                        return [false, $result->result->errorMessage];

                    else return [true, 'Ürün kaydedildi.', '/urun/n11/'. $id];
                }

                else return [false, $result];
            }

            else if ($r->get('task') == 'sil')
            {
                $result = Controller::n11('ProductService', 'DeleteProductById',
                [
                    'productId' => $id 
                ]);

                if (isset($result->result->status) && $result->result->status == 'success')

                    return [true, 'Ürün silindi.', '/urun/n11'];

                else return [false, 'Sorgu hatası..'];
            }
        }

        else
        {
            if ($id)
            {
                $result = Controller::n11('ProductService', 'GetProductByProductId',
                [
                    'productId' => $id,
                ]);

                if (isset($result->product))
                
                    $data['incoming'] = $result->product;

                // dd($data['incoming']); exit;
            }

            if (!isset($data['incoming']))
            {
                if ($r->has('s'))
                {
                    $system = Product::where('product.id', $r->get('s'))
                        ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
                        ->leftJoin('category', 'category.id', '=', 'product.category_id')
                        ->selectRaw('IFNULL(JSON_EXTRACT(category.match, "$.n11"), "null") AS category_id_n11')
                        ->selectRaw('product.*, brand.name AS brand')
                        ->first();
                }

                else
                {
                    $system = new Product;

                    $system->category_id_n11 = 'null';

                    $system->domestic = 0;
                }

                $system->stockItems = 'null';

                $photos = Photo::where('product_id', $system->id)->orderBy('profile', 'desc')->orderBy('order')->get();

                if (count($photos) > 0)
                {
                    $temp = [];

                    foreach ($photos as $photo)

                        $temp[] = '{"url":"'. url('assets/images/products/'. $photo->name) .'", "order":'. $photo->order .'}';

                    $temp = '{"image":['. implode(',', $temp) .']}';
                }

                else $temp = 'null';

                if ($system->category_id_n11 != 'null')

                    $system->category_id_n11 = '{"id":'. $system->category_id_n11 .'}';

                if ($system->id)
                {
                    $temps_p = Product::where('model_code', $system->model_code)
                        ->selectRaw('*, IF(code = "'. $system->code .'", 1, 0) AS control')
                        // ->where('code', '!=', $system->code)
                        ->where('model_code', '!=', null)
                        ->where('del', 0)
                        ->orderBy('control', 'DESC')
                        ->get();

                    if (count($temps_p) > 0)
                    {
                        $stock = [];

                        foreach ($temps_p as $temp_p)
                        {
                            $stock[] = 
                            
                            '{
                                "quantity":'. $temp_p->stock .',
                                "optionPrice":'. $temp_p->price .',
                                "sellerStockCode":"'. $temp_p->code .'",
                                "attributes": {"attribute":
                                [
                                    {"name": null, "value": null},
                                    {"name": null, "value": null},
                                    {"name": null, "value": null}
                                ]},
                                "n11CatalogId": null
                            }';
                        }

                        $system->stockItems = '{"stockItem":['. implode(',', $stock) .']}';
                    }
                }

                $names = array_filter([$system->brand, $system->name, $system->code, $system->model_code]);

                $system->name = implode(' ', array_unique($names));

                $columns = 

                '{
                    "id": null,
                    "title": "'. $system->name .'",
                    "subtitle": "'. $system->pre_description .'",
                    "displayPrice": '. $system->discount .',
                    "price": '. $system->price .',
                    "productSellerCode": "'. $system->code .'",
                    "description": "'. htmlentities($system->description) .'",
                    "category": '. $system->category_id_n11 .',
                    "specialProductInfoList": null,
                    "preparingDay": 3,
                    "domestic": '. $system->domestic .',
                    "saleStartDate": null,
                    "saleEndDate": null,
                    "productCondition": null,
                    "images": '. $temp .',
                    "stockItems": '. $system->stockItems .',
                    "discount": null,
                    "shipmentTemplate": "Kargo",
                    "attributes": null,
                    "approvalStatus": null,
                    "saleStatus": null,
                    "currencyAmount": null,
                    "currencyType": 0,
                    "unitInfo": null,
                    "stock": '. $system->stock .'
                }';

                $data['incoming'] = json_decode(preg_replace('/[[:cntrl:]]/', '', $columns));
            }

            $data['categories'] = $this->category_n11(true);

            $data['shipments'] = Controller::n11('ShipmentService', 'GetShipmentTemplateList');

            return view('product.n11-e', $data);
        }
    }

    // gittigidiyor

    public function category_g_g($object = false)
    {
        $results = Controller::g_g('CategoryService', 'getParentCategories');

        $categories = [];

        if (isset($results->categories->category))
        {
            if (!is_array($results->categories->category))

                $categories = [$results->categories->category];

            else $categories = $results->categories->category;

            $temp = [];

            foreach ($categories as $category)
            {
                $temp[] = (object) 
                [
                    'id' => $category->categoryCode, 
                    'name' => $category->categoryName
                ];
            }

            $categories = $temp;

            // usort($categories, fn($a, $b) => strcmp($a->categoryName, $b->categoryName));
        }

        if (!$object)
        {
            $temp = [];

            foreach ($categories as $category)
            
                $temp[$category->id] = $category->name;

            $categories = $temp;
        }

        return $categories;
    }

    public function list_g_g(Request $r)
    {
        $data['products'] = Product::where('del', 0)->orderBy('name')->get();

        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $status = $data['status'] = $r->has('status') ? $r->get('status') : 'A';

        $data['datas'] = Controller::g_g('IndividualProductService', 'getProducts',
        [
            'startOffSet' => ($page - 1) * 100,
            'rowCount' => $show,
            'status' => $status,
            'withData' => true,
        ]);

        return view('product.g_g', $data);
    }

    public function index_g_g(Request $r, $id = null)
    {
        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $xml = 
                [
                    'cargoDetail' =>
                    [
                        'city' => '34',
                        'shippingPayment' => 'S',
                        'shippingWhere' => 'country',
                        'cargoCompanyDetails' =>
                        [
                            'cargoCompanyDetail' =>
                            [
                                'name' => 'aras',
                                'cityPrice' => '3.00',
                                'countryPrice' => '5.00',
                            ]
                        ],
                        'shippingTime' =>
                        [
                            'days' => '2-3days',
                            // 'beforeTime' => '10:00',
                        ]
                    ]
                ];

                foreach ($r->all() as $name => $value)
                {
                    if (in_array($name, ['task', '_token']))

                        continue;

                    $xml[$name] = $value;
                }

                if ($r->hasFile('profile'))
                {
                    $photo = Controller::file($r->file('profile'));
                        
                    if (!$photo[0])

                        return [false, $photo[1]];

                    $name = mt_rand() .'.'. $photo[1]->getClientOriginalExtension();

                    $r->file('profile')->move('assets/images/products', $name);

                    $xml['photos']['photo'][] = 
                    [
                        'url' => url('assets/images/products/'. $name),
                        // 'order' => 1
                    ];

                    $j = 2;

                    foreach ($xml['photos']['photo'] as $i => $image)
                    {
                        if ($image['url'] == url('assets/images/products/'. $name))

                            continue;

                        // $xml['images']['image'][$i]['order'] = $j;

                        $j++;
                    }
                }

                if ($r->has('photo-del'))
                {
                    foreach ($xml['photos']['photo'] as $i => $image)
                    {
                        if (in_array($image['url'], $r->get('photo-del')))

                            unset($xml['photos']['photo'][$i]);
                    }
                }

                if ($r->hasFile('photo-add'))
                {
                    foreach ($r->file('photo-add') as $photo)
                    {
                        $photo = Controller::file($photo);
                        
                        if (!$photo[0])

                            return [false, $photo[1]];
                    }

                    foreach ($r->file('photo-add') as $i => $photo)
                    {
                        $name = mt_rand() .'.'. $photo->getClientOriginalExtension();

                        $r->file('photo-add')[$i]->move('assets/images/products', $name);

                        $xml['photos']['photo'][] = 
                        [
                            'url' => url('assets/images/products/'. $name),
                            // 'order' => count($xml['photos']['photo']) + 1
                        ];
                    }
                }

                if (isset($xml['specs']['spec']))
                {
                    foreach ($xml['specs']['spec'] as $i => $attribute)
                    {
                        if ($attribute['custom'])

                            $xml['specs']['spec'][$i]['value'] = $attribute['custom'];

                        if (!$xml['specs']['spec'][$i]['value'])

                            unset($xml['specs']['spec'][$i]);

                        unset($xml['specs']['spec'][$i]['custom']);
                    }

                    sort($xml['specs']['spec']);
                }

                /* if (isset($xml['stockItems']['stockItem']))
                {
                    foreach ($xml['stockItems']['stockItem'] as $j => $item)
                    {
                        if (isset($item['specs']['spec']))
                        {
                            foreach ($item['specs']['spec'] as $i => $attribute)
                            {
                                if (isset($attribute['custom']) && $attribute['custom'])
                                {
                                    $item['specs']['spec'][$i]['value'] = $attribute['custom'];

                                    unset($item['specs']['spec'][$i]['custom']);
                                }

                                if (!$item['specs']['spec'][$i]['value'])

                                    unset($item['specs']['spec'][$i]);
                            }

                            sort($item['specs']['spec']);

                            $xml['stockItems']['stockItem'][$j] = $item;
                        }
                    }
                } */

                foreach ($xml as $name => $value)
                {
                    if ($name != 'itemId' && $name != 'product')
                    {
                        $xml['product'][$name] = $value;

                        unset($xml[$name]);
                    }
                }

                $order = ['categoryCode', 'storeCategoryId', 'title', 'subtitle', 'specs', 'photos', 'pageTemplate', 
                    'description', 'startDate', 'catalogId', 'catalogDetail', 'catalogFilter', 'format', 'startPrice', 
                    'buyNowPrice', 'netEarning', 'listingDays', 'productCount', 'cargoDetail', 'affiliateOption', 
                    'boldOption', 'catalogOption', 'vitrineOption'];

                $param = [];

                foreach ($order as $column)
                {
                    if (isset($xml['product'][$column]))

                        $param[$column] = $xml['product'][$column];

                    else $param[$column] = null;
                }

                $xml['product'] = $param;

                $xml['forceToSpecEntry'] = $xml['nextDateOption'] = false;

                // return [false, $xml];

                $result = Controller::g_g('IndividualProductService', 'insertProductWithNewCargoDetail', $xml);

                return [false, $result];

                if (isset($result->ackCode))
                {
                    if ($result->ackCode == 'failure')

                        return [false, $result->error->message];

                    else return [true, 'Ürün kaydedildi.', '/urun/n11/'. $id];
                }

                else return [false, $result];
            }

            else if ($r->get('task') == 'sil')
            {
                $result = Controller::n11('ProductService', 'DeleteProductById',
                [
                    'productId' => $id 
                ]);

                if (isset($result->result->status) && $result->result->status == 'success')

                    return [true, 'Ürün silindi.', '/urun/n11'];

                else return [false, 'Sorgu hatası..'];
            }
        }

        else
        {
            if ($id)
            {
                $result = Controller::g_g('IndividualProductService', 'getProduct',
                [
                    'productId' => $id,
                ]);

                if (isset($result->productDetail))
                
                    $data['incoming'] = $result->productDetail;

                // dd($data['incoming']); exit;
            }

            else
            {
                $columns =
                
                '';
            }

            $data['categories'] = $this->category_g_g(true);

            return view('product.g_g-e', $data);
        }
    }

    // ciceksepeti

    public function list_c_s(Request $r)
    {
        $data['products'] = Product::has('brand')->where('del', 0)->orderBy('name')->get();

        $show = $data['show'] = 60;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $param =
        [
            'PageSize' => $show, 
            'Page' => $page
        ];

        if ($r->has('code'))

            $param['StockCode'] = $r->get('code');

        $data['datas'] = Controller::c_s('GET', 'Products', $param);
        
        return view('product.c_s', $data);
    }

    public function category_c_s($object = false)
    {
        $results = Controller::c_s('GET', 'Categories');

        if (!isset($results->categories))

            return [];

        $results = json_encode($results->categories);

        $results = json_decode($results, true);

        $temp = $level = [];

        $j = $k = 0;

        array_walk_recursive($results, function($a, $i) use (&$temp, &$j, &$k, &$level)
        {
            if ($i == 'id')
            
                $j = $a;

            else if ($i == 'name')
            
                $temp[$j] = $a;

            else if ($i == 'parentCategoryId' && $a)
            {
                $temp[$j] = $temp[$a] .' > '. $temp[$j];

                $level[$a] = $temp[$j];
            }
        });

        $categories = [];

        foreach ($temp as $id => $name)
        {
            if (!isset($level[$id]))
            {
                if ($object)

                    $categories[] = (object) ['id' => $id, 'name' => $name];

                else $categories[$id] = $name;
            }
        }

        return $categories;
    }

    public function index_c_s(Request $r, $id = null)
    {
        if ($id)
        {
            $result = Controller::c_s('GET', 'Products',
            [
                'StockCode' => $id
            ]);

            if (isset($result->products))

                $data['incoming'] = $result->products[0];
        }

        if (!isset($data['incoming']))
        {
            if ($r->has('s'))
            {
                $system = Product::where('product.id', $r->get('s'))
                    ->leftJoin('category', 'category.id', '=', 'product.category_id')
                    ->leftJoin('brand', 'brand.id', '=', 'product.brand_id')
                    ->selectRaw('IFNULL(JSON_EXTRACT(category.match, "$.ciceksepeti"), "null") AS category_id_ciceksepeti')
                    ->selectRaw('product.*, JSON_UNQUOTE(JSON_EXTRACT(brand.match_name, "$.ciceksepeti")) AS brand_name')
                    ->selectRaw('IFNULL(JSON_EXTRACT(brand.match, "$.ciceksepeti"), "null") AS brand_id_ciceksepeti')
                    ->selectRaw('brand.name AS brand_system')
                    ->first();
            }

            else
            {
                $system = new Product;

                $system->category_id_ciceksepeti = 'null';

                $system->brand_name = null;

                $system->brand_id_ciceksepeti = 'null';
            }

            $photos = Photo::where('product_id', $system->id)->orderBy('profile', 'DESC')->orderBy('order')->get();

            if (count($photos) > 0)
            {
                $temp = [];

                foreach ($photos as $photo)

                    $temp[] = '"'. url('assets/images/products/'. $photo->name) .'"';

                $temp = implode(',', $temp);
            }

            else $temp = null;

            $names = array_filter([$system->brand_system, $system->name, $system->code, $system->model_code]);

            $system->name = implode(' ', array_unique($names));

            $columns =

            '{
                "productName": "'. $system->name .'",
                "mainProductCode": "'. $system->model_code .'",
                "stockCode": "'. $system->code .'",
                "categoryId": '. $system->category_id_ciceksepeti .',
                "description": "'. htmlentities($system->description) .'",
                "deliveryMessageType": 5,
                "deliveryType": 2,
                "stockQuantity": '. $system->stock .',
                "salesPrice": '. $system->discount .',
                "listPrice": '. $system->price .',
                "barcode": "'. $system->barcode .'",
                "images": ['. $temp .'],
                "attributes": []
            }';

            $data['incoming'] = json_decode(preg_replace('/[[:cntrl:]]/', '', $columns));
        }

        if ($r->isMethod('post'))
        {
            if ($r->get('task') == 'kaydet')
            {
                $temp = [];

                foreach ($r->all() as $name => $value)
                {
                    if (in_array($name, ['s', 'task', '_token', 'photo-add', 'photo-del']))

                        continue;

                    if (is_array($value))
                    {
                        if (count($value) > 0)

                            $temp[$name] = $value;
                    }

                    else if (!is_null($value))

                        $temp[$name] = $value;
                }

                if ($temp['salesPrice'] < $temp['listPrice'] / 1.2)

                    $temp['listPrice'] = $temp['salesPrice'] * 1.2;

                if (isset($temp['attributes']))
                {
                    $attributes = [];

                    foreach ($temp['attributes'] as $attribute)
                    {
                        if (isset($attribute['valueId']))
                        {
                            if (!is_null($attribute['valueId']))

                                $attributes[] = $attribute;
                        }
                    }

                    $temp['attributes'] = $attributes;
                }
                
                if ($r->hasFile('photo-add'))
                {
                    if (!isset($temp['photos']))

                        $temp['photos'] = [];

                    foreach ($r->file('photo-add') as $photo)
                    {
                        $photo = Controller::file($photo);
                        
                        if (!$photo[0])

                            return [false, $photo[1]];
                    }

                    foreach ($r->file('photo-add') as $i => $photo)
                    {
                        $name = mt_rand() .'.'. $photo->getClientOriginalExtension();

                        $r->file('photo-add')[$i]->move('assets/images/products', $name);

                        $temp['photos'][] = url('assets/images/products/'. $name);
                    }
                }

                if (isset($temp['photos']))
                {
                    if ($r->has('photo-del'))
                    
                        $temp['photos'] = array_diff($temp['photos'], $r->get('photo-del'));

                    if (count($temp['photos']) > 0)
                    {
                        $photos = [];

                        foreach ($temp['photos'] as $photo)
                        
                            $photos[] = $photo;

                        $temp['photos'] = $photos;
                    }

                    else unset($temp['photos']);
                }

                if (isset($temp['photos']))
                {
                    $temp['images'] = $temp['photos'];

                    unset($temp['photos']);
                }

                $method = $id ? 'PUT' : 'POST';

                $result = Controller::c_s($method, 'Products', ['products' => [$temp]]);

                return isset($result->message) ? 
                
                    [false, $result->message] : 
                    
                    [true, 'Ürün kaydedildi.', '/urun/ciceksepeti/'. $id];
            }
        }

        else
        {
            $data['categories'] = $this->category_c_s();

            $data['cargos'] = 
            [
                1 => 'Servis Aracı ile Gönderim',
                2 => 'Kargo ile Gönderim',
                3 => 'Kargo + Servis Aracı ile Gönderim'
            ];

            $data['cargo_types'] = 
            [
                1 => 'Çiçek Servis',
                4 => 'Hediye Kargo Aynı Gün',
                18 => 'Hediye Kargo 1-2 İş Günü',
                5 => 'Hediye Kargo 1-3 İş Günü',
            ];

            // return var_dump($data['incoming']);

            if ($data['incoming']->categoryId)
            {
                $results = Controller::c_s('GET', 'Categories/'. $data['incoming']->categoryId .'/attributes');

                if (isset($results->categoryAttributes))
                
                    $data['attributes'] = $results->categoryAttributes;
            }

            return view('product.c_s-e', $data);
        }
    }

    // hepsiburada
    
    public function list_h_b(Request $r)
    {
        $data['products'] = Product::has('brand')->where('del', 0)->orderBy('name')->get();

        $show = $data['show'] = 100;

        $page = $data['page'] = $r->has('page') ? $r->get('page') : 1;

        $limit = $data['limit'] = ($page * ($show + 1)) - ($page + $show);

        $param =
        [
            'limit' => $show, 
            'offset' => ($page - 1) * $show
        ];

        $data['datas'] = self::h_b('GET', ['listing', 'listings'], $param);
        
        return view('product.h_b', $data);
    }
}
