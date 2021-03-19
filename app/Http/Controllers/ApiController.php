<?php

namespace App\Http\Controllers;

use \App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        LogActivity::addToLog('My Testing Add To Log.');
        $logs = LogActivity::logActivityLists();
        return view('hhs', compact('logs'));
    }

    public function userLog(Request $request){
    return $logs = LogActivity::logActivityLists();
    }



    public function serverLog(Request $request)
    {
        //baca file
        switch ($request->mode) {
            case 1:
                $data = array();
                $disk = Storage::disk('custom');
                $files = $disk->get('access.log');
                // return $files;
                $matches = array();
                $re = '`^(\S+) (\S+) (\S+) \[([\w:/]+\s[+\-]\d{4})\] \"(\S+) (\S+)\s*(\S+)?\s*\" (\d{3}) (\S+)`ms';
      
                preg_match_all($re, $files, $matches, PREG_SET_ORDER, 0);
                $slices = 0;
                // return $matches;
                if (count($matches) > 0 && count($matches) < 150) {
                    $percentage = round(count($matches) * 0.50);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }
                if (count($matches) > 150 && count($matches) < 500) {
                    $percentage = round(count($matches) * 0.90);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }

                if (count($matches) > 500 && count($matches) < 1000) {
                    $percentage = round(count($matches) * 0.90);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }

                if (count($matches) > 1000 && count($matches) < 2000) {
                    $percentage = round(count($matches) * 0.95);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }
                // Print the entire match result
                // var_dump($matches);
                return $slices;
                break;
            case 2:
                //baca file
                $data = array();
                $disk = Storage::disk('custom');
                $files = $disk->get('error.log');
                // return $files;
                $matches = array();
                $re = '/^(\[[^\]]+\]) (\[[^\]]+\]) (\[[^\]]+\]) (.*)$/m';
    
                preg_match_all($re, $files, $matches, PREG_SET_ORDER, 0);
                $slices = 0;
                // return $matches;
                if (count($matches) > 0 && count($matches) < 150) {
                    $percentage = round(count($matches) * 0.50);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }
                if (count($matches) > 150 && count($matches) < 500) {
                    $percentage = round(count($matches) * 0.90);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }

                if (count($matches) > 500 && count($matches) < 1000) {
                    $percentage = round(count($matches) * 0.90);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }

                if (count($matches) > 1000 && count($matches) < 2000) {
                    $percentage = round(count($matches) * 0.95);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }
                if (count($matches) > 2000) {
                    $percentage = round(count($matches) * 0.96);
                    // return $percentage;
                    $slices = array_slice($matches, $percentage, count($matches) - 1);
                }
                // Print the entire match result
                // var_dump($matches);
                return $slices;
                break;
        }
    }

    public function restartServer(Request $request)
    {
        pclose(popen("start /B " . "da.bat", "r"));
        $tv =array();
        $tv['isSuccess'] = true;
        $tv['message'] = 'Respon Aslinya ga Gini, tapi Error 500, karena langsung shutdown server!';
        return $tv;
        return pclose(popen("start /B " . "da.bat", "r"));
    }

    public function getRam(Request $request)
    {
        $finalData = array();
        $prepareData = array();
        $dataFirst = array();
        $outputCommand = array();
        $state = true;
        exec('wmic memorychip get/format:list ', $outputCommand);

        foreach ($outputCommand as $item) {
            if ($item != '') {
                $dataFirst[] = $item;
            }
        }

        foreach ($dataFirst as $item) {
            if ($item == "Attributes=1") {
                $state = true;
                continue;
            }
            if ($item == "Attributes=2") {
                $prepareData = array();
                $state = false;
                continue;
            }
            if ($state) {
                $prepareData[] = $item;
                $finalData['ram1'] = $prepareData;
            } else {
                $prepareData[] = $item;
                $finalData['ram2'] = $prepareData;
            }
        }
        return $finalData;
    }


    public function getProcess(Request $request)
    {
        $command = 'tasklist';
        $data = array();
        $wordCount = array();
        $outputData = array();
        exec($command, $outputData);

        $stripExplode = explode(" ", $outputData[2]);
        foreach ($stripExplode as $item) {
            $wordCount[] = \strlen($item);
        }

        $a = $wordCount[0];
        $b = $a + 1;
        $c = $b + $wordCount[1] + 1;
        $d = $c + $wordCount[2] + 1;
        $e = $d + $wordCount[3] + 1;

        $i = 0;
        foreach ($outputData as $item) {
            if ($i > 2) {
                $it = array();
                $it['image_name'] = trim(\substr($item, 0, $wordCount[0]));
                $it['pid'] = trim(\substr($item, $b, $wordCount[1]));
                $it['session_name'] = trim(\substr($item, $c, $wordCount[2]));
                $it['session'] = trim(\substr($item, $d, $wordCount[3]));
                $it['memory'] = trim(\substr($item, $e, $wordCount[4]));
                $data[] = $it;
            }
            $i++;
        }
        $f = array();
        $f['data'] = $data;
        return $f;
    }

    public function getSystemInfo(Request $request)
    {
        $dataFirst = array();
        $outputCommand = array();
        exec('systeminfo', $outputCommand);

        foreach ($outputCommand as $item) {
            $dataRepo = array();
            $explodeFirst =  explode(":", $item);

            if (count($explodeFirst) > 1) {
                $key = str_replace(" ", "", $explodeFirst[0]);
                $dataRepo[$key] = $explodeFirst[1];
            }
            $dataFirst[] = $dataRepo;
        }
        $finalData = array();
        $childData = array();
        $oldKey = '';
        foreach ($dataFirst as $item) {
            foreach ($item as $key => $value) {
                if (str_contains($key, "[")) {
                    $childData[] = trim($value);
                    if ($oldKey != '') {
                        $finalData[$oldKey] = $childData;
                    }
                } else {
                    $childData = array();
                    $finalData[$key] = trim($value);
                    $oldKey = $key;
                }
            }
        }
        return '{"data":'.$finalData.'}';
    }

    public function getServerLog(Request $request)
    {
        //baca file
        $data = array();
        $disk = Storage::disk('custom');
        $files = $disk->allFiles();
        foreach ($files as $item) {
            $name = explode('.', $item);
            $dataProperties = array();
            $dataProperties['file_name'] = $name[0];
            $dataProperties['file_type'] = $name[1];
            $dataProperties['last_time_modified'] = $disk->lastModified($item);
            $dataProperties['file_size'] = $disk->size($item);
            $data[] = $dataProperties;
        }
        $j = array();
        $j['data'] = $data;
        return $j;
    }
}
