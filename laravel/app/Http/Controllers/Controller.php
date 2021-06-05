<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

use App\Models\Data;

use Validator, Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test(Request $r)
    {
        // ..
    }

    public function ctg_update()
    {
        $stores = \App\Models\Data::pluck('name')->toArray();

        foreach (\App\Models\Category::all() as $data)
        {
            $commision = json_decode($data->commision, true);

            $match = json_decode($data->match, true);

            foreach ($stores as $store)
            {
                if (!isset($match[$store]))

                    $match[$store] = null;

                if (!isset($commision[$store]))

                    $commision[$store] = 0;
            }

            $data->match = json_encode($match, JSON_NUMERIC_CHECK);

            $data->commision = json_encode($commision, JSON_NUMERIC_CHECK);

            $data->save();
        }
    }

    public function dropzone(Request $r)
    {
        $input = $r->all();

        $rules = ['file' => 'image|max:'. (3 * 1024)];

        $validation = Validator::make($input, $rules);

        if ($validation->fails())
        
            return Response::make(['error' => $validation->errors()->first()], 400);

        $file = $_FILES['file']; // $r->file('file');

        $extension = File::extension($file['name']);
        
        $directory = public_path() .'/assets/images/uploads';
        
        $filename = mt_rand() .".{$extension}";

        $upload_success = move_uploaded_file($file["tmp_name"], $directory .'/'. $filename);

        if ($upload_success)

            return Response::json($filename, 200);
        
        else return Response::json(['error' => 'Dosya yüklenemedi.'], 400);
    }

    public function setting(Request $r, $id)
    {
        $data['incoming'] = $incoming = Data::find($id);

        if ($r->isMethod('post'))
        {
            $incoming->data = json_encode($r->get('data'), JSON_NUMERIC_CHECK);

            $incoming->active = $r->has('active') ? 1 : 0;

            $incoming->save();

            return [true, 'Ayar kaydedildi.', '/ayar/'. $id];
        }

        else
        {
            $data['datas'] = Data::orderBy('name')->get();

            return view('setting', $data);
        }
    }

    public function clear_cache()
    {   
        foreach (['view', 'config', 'route', 'cache'] as $type)

            \Artisan::call($type .':clear');

        File::delete(File::files('assets/images/uploads'));
    }

    public static function c_s($method, $path, $param = null)
    {
        $data = Data::where('name', 'ciceksepeti')->first();

        if (!$data)

            return null;

        $data = json_decode($data->data);

        $url = $data->test ? 'sandbox-' : '';

        $url = 'https://'. $url .'apis.ciceksepeti.com/api/v1/';

        $url .= $path;

        $curl = curl_init();

        if (is_array($param))
        {
        	/* $file_name = str_replace('/', '-', $path) .'-'. $method .'-'. mt_rand() .'.json';

            File::put(public_path('xml-datas/'. $file_name), json_encode($param, JSON_NUMERIC_CHECK)); */
        	
            if ($method == 'GET')
            {
                $temp = [];

                foreach ($param as $key => $value)
                
                    $temp[] = $key .'='. $value;

                $url .= '?'. implode('&', $temp);
            }

            else 
            {
                $temp = json_encode($param, JSON_NUMERIC_CHECK);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $temp);
            }
        }

        $api_key = $data->test ? $data->test_api_key : $data->api_key;

        curl_setopt_array($curl,
        [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER =>
            [
                'x-api-key: '. $api_key,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($curl);

        $response = json_decode($response);

        curl_close($curl);
        
        return $response;
    }

    public static function trendyol($method, $path, $param = null, $id = true)
    {
        $curl = curl_init();

        $url = 'https://api.trendyol.com/sapigw/';

        $data = Data::where('name', 'trendyol')->first();

        if (!$data)

            return null;

        $data = json_decode($data->data);

        if ($id)

            $url .= 'suppliers/'. $data->supplier_id .'/';

        $url .= $path;

        if (is_array($param))
        {
        	/* $file_name = strtolower($method .'-'. $path .'-'. mt_rand() .'.json');

            File::put(public_path('xml-datas/'. $file_name), json_encode($param, JSON_NUMERIC_CHECK)); */
        	
            if ($method == 'GET')
            {
                $temp = [];

                foreach ($param as $key => $value)
                
                    $temp[] = $key .'='. $value;

                $url .= '?'. implode('&', $temp);
            }

            else 
            {
                $temp = json_encode($param, JSON_NUMERIC_CHECK);

                curl_setopt($curl, CURLOPT_POSTFIELDS, $temp);
            }
        }

        curl_setopt_array($curl,
        [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HEADER => false,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER =>
            [
                'Authorization: Basic '. base64_encode($data->api_key .':'. $data->api_secret),
                'Content-Type: application/json',
            ],
            CURLOPT_USERAGENT => $data->supplier_id .' - Ruberu',
            CURLOPT_VERBOSE => TRUE,
            CURLOPT_STDERR => $verbose = fopen('php://temp', 'rw+'),
            CURLOPT_FILETIME => TRUE,
        ]);

        $response = curl_exec($curl);

        $info = "Verbose information:\n". !rewind($verbose) . stream_get_contents($verbose) ."\n";

        $response = json_decode($response);

        curl_close($curl);

        return $response;
    }

    public static function n11($path, $function, $param = null)
    {
        $data = Data::where('name', 'n11')->first();

        if (!$data)

            return null;

        $data = json_decode($data->data);

        $url = 'https://api.n11.com/ws/';

        $cons = 
        [
            'auth' => 
            [
                'appKey' => $data->api_key, // 'ea88b14f-073d-4d30-9ffd-377ebb87e3a4',
                'appSecret' => $data->api_secret, // 'RiDx2fGv2B8IlGhy',
            ]
        ];

        if ($param)
        {
            foreach ($param as $key => $value)

                $cons[$key] = $value;
        }

        if (!isset($param['pagingData']))
        {
            $cons['pagingData'] =
            [
                'currentPage' => 0,
                'pageSize' => 100
            ];
        }

        /* $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');

        self::array_to_xml($cons, $xml_data);

        $xml_data->asXML(public_path('xml-datas/'. rand() .'.xml')); */

        $soap = new \SoapClient($url . $path .'.wsdl', ['trace' => true]);

        try
        {
            return $soap->$function($cons);
        }

        catch(\Throwable $e) 
        {
            return $e->getMessage();
        }
        
        catch(\SoapFault $e)
        {
            return $e->getMessage();
        }
        
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    public static function parasut($path, $function, $param = null, $id = null)
    {
        $client = new \App\Http\Parasut\Client(
        [
            "client_id" => "edbe4b7d8dc0e71fc7b799fb6205052974e1beb5b060cc87d34c2add4af36ea3",
            "client_secret" => "ab630276fad187f460e7254721803cf93a090fab3c2a0bdf5f4382b34a7976db",
            "username" => "info@noone.com.tr",
            "password" => "Ru!2204426",
            "grant_type" => "password",
            "redirect_uri" => "urn:ietf:wg:oauth:2.0:oob",
            "company_id" => "388522"
        ]);

        $class = '\\App\\Http\\Parasut\\'. ucfirst($path);

        $data = new $class($client);

        if (!is_array($param)) :

            /* $file_name = $path .'-'. $function .'-'. mt_rand() .'.json';

            File::put(public_path('xml-datas/'. $file_name), $param); */

            $param = json_decode($param, true);

        endif;
        
        return $id ? $data->$function($id, $param) : $data->$function($param);
    }

    public static function g_g($path, $function, $param = null)
    {
        $url = 'https://dev.gittigidiyor.com:8443/listingapi/ws/';

        $data = Data::where('name', 'gittigidiyor')->first();

        if (!$data)

            return null;

        $data = json_decode($data->data);

        $auth =
        [
            'login' => $data->role_name,
            'password' => $data->role_pass,
            'authentication' => SOAP_AUTHENTICATION_BASIC
        ];

        $soap = new \SoapClient($url . $path .'?wsdl', $auth);

        $param['lang'] = 'tr';

        if (preg_match('/Individual/', $path))
        {
            $temp = ['apiKey' => $data->api_key];

            list($usec, $sec) = explode(' ', microtime());
            
            $time = round(((float) $usec + (float) $sec) * 100) .'0';
            
            $temp['sign'] = md5($data->api_key . $data->secret_key . $time);

            $temp['time'] = $time;

            foreach ($param as $key => $value)

                $temp[$key] = $value;

            $param = $temp;
        }

        /* $xml_data = new \SimpleXMLElement('<?xml version="1.0"?><data></data>');

        self::array_to_xml($param, $xml_data);

        $xml_data->asXML(public_path('xml-datas/'. rand() .'.xml')); */

        try
        {
            return $soap->__soapCall($function, $param);
        }

        catch(\Throwable $e) 
        {
            return $e->getMessage();
        }
        
        catch(\SoapFault $e)
        {
            return $e->getMessage();
        }
        
        catch(\Exception $e)
        {
            return $e->getMessage();
        }
    }

    public static function file($data)
    {
        $type = strtolower($data->getClientOriginalExtension());

        $types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'jfif'];
        
        if (!in_array($type, $types))
        
            return [false, 'Dosya '. implode(', ', $types) .' formatlarından biri olmalı.'];

        /* if ($data->getSize() > 2048000)

            return array(false, 'Dosya boyutu max. 2MB olmalı!'); */

        return [true, $data];
    }

    public static function url($data, $table = 'product', $db = 'mysql', $limit = 255)
    {
        $turkce = array("ş", "Ş", "ı", "ü", "Ü", "ö", "Ö", "ç", "Ç", "ş", "Ş", "ı", "ğ", "Ğ", "İ", "ö", "Ö", "Ç", "ç", "ü", "Ü");
        $duzgun = array("s", "S", "i", "u", "U", "o", "O", "c", "C", "s", "S", "i", "g", "G", "I", "o", "O", "C", "c", "u", "U");
        
        $text = str_replace($turkce, $duzgun, $db == 'mysql' ? $data->name : $data->isim);
        
        $text = preg_replace("@[^a-z0-9\-_şıüğçİŞĞÜÇ]+@i",  " ",  $text);
        
        $text = preg_replace('~[^\\pL\d]+~u', '-', $text);
        
        $text = trim($text, '-');
        
        $text = mb_strtolower($text);
        
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        if (empty($text)) 
        
            return false;

        $kontrol = \DB::connection($db)->table($table)->where('url', $text)->where('id', '!=', $data->id)->count();
        
        if ($kontrol > 0) 
        
            $text .= '-'. md5(mt_rand());

        return $text;
    }

    public static function array_to_xml($data, &$xml_data) 
    {
        foreach ($data as $key => $value) 
        {
            if (is_numeric($key))
                
                $key = 'item'. $key; //dealing with <0/>..<n/> issues
            
            if (is_array($value)) 
            {
                $subnode = $xml_data->addChild($key);
                
                self::array_to_xml($value, $subnode);
            } 
            
            else $xml_data->addChild("$key", htmlspecialchars("$value"));
         }
    }

    public function my_walk_recursive(array $array, $path = null) 
    {
        foreach ($array as $k => $v)
        {
            echo $path ? $path .' > '. $k : $k;

            echo "\n";

            if (is_array($v))
            {
                if (isset($v[0]))
                {
                    $v = $v[0];

                    if ($path)

                        $k = $path .' > '. $k;
                }

                $this->my_walk_recursive($v, $k);
            }
        }
    }
}
