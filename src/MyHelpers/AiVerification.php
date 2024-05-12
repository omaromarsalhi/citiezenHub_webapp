<?php

namespace App\MyHelpers;

use Symfony\Component\HttpClient\HttpClient;


class AiVerification
{
    private $aiDataHolder;


    public function run($obj): AiDataHolder
    {
        $this->aiDataHolder = new AiDataHolder();;
        $this->getAllDesc($obj['images']);
        $this->compareDescWithTitleAndCategory($this->aiDataHolder->getDescriptions(), $obj);
        return $this->aiDataHolder;
    }

    public function runOcr($obj): void
    {
        $this->getOcrResult($obj['pathFrontCin'], $obj['fileNameFront']);
        $this->getOcrResult($obj['pathBackCin'], $obj['fileNameBackCin']);
        $this->formatJsonFilesOfCin($obj['fileNameFront'], $obj['fileNameBackCin']);
    }

    public function formatJsonFilesOfCin($filePathFrontCin, $filePathBackCin): void
    {
        $pathFrontCin = '../../files/usersJsonFiles/' . $filePathFrontCin . '.json';
        $pathBackCin = '../../files/usersJsonFiles/' . $filePathBackCin . '.json';

        $jsonString = file_get_contents($pathFrontCin);
        $jsonDataFrontCin = json_decode($jsonString, true);

        $jsonString = file_get_contents($pathBackCin);
        $jsonDataBackCin = json_decode($jsonString, true);

        $bounding_boxesFront = $jsonDataFrontCin['google']['bounding_boxes'];
        $bounding_boxesBack = $jsonDataBackCin['google']['bounding_boxes'];

        $userCinData = [];
        for ($i = 0; $i < sizeof($bounding_boxesFront); $i++) {
            switch ($bounding_boxesFront[$i]['text']) {
                case 'اللقب':
                    $userCinData['اللقب']['top'] = $bounding_boxesFront[$i]['top'];
                    $userCinData['اللقب']['topPlusHeight'] = $bounding_boxesFront[$i]['height'] + $bounding_boxesFront[$i]['top'];
                    $userCinData['اللقب']['data'] = '';
                    break;
                case 'الاسم':
                    $userCinData['الاسم']['top'] = $bounding_boxesFront[$i]['top'];
                    $userCinData['الاسم']['topPlusHeight'] = $bounding_boxesFront[$i]['height'] + $bounding_boxesFront[$i]['top'];
                    $userCinData['الاسم']['data'] = '';
                    break;
                case 'بن':
                    if (!array_key_exists('بن', $userCinData)) {
                        $userCinData['بن']['top'] = $bounding_boxesFront[$i]['top'];
                        $userCinData['بن']['topPlusHeight'] = $bounding_boxesFront[$i]['height'] + $bounding_boxesFront[$i]['top'];
                        $userCinData['بن']['data'] = '';

                    }
                    break;
                case'الولادة':
                    $userCinData['تاريخ الولادة']['top'] = $bounding_boxesFront[$i]['top'];
                    $userCinData['تاريخ الولادة']['topPlusHeight'] = $bounding_boxesFront[$i]['height'] + $bounding_boxesFront[$i]['top'];
                    $userCinData['تاريخ الولادة']['data'] = '';
                    break;
                case 'مكانها':
                    $userCinData['مكانها']['top'] = $bounding_boxesFront[$i]['top'];
                    $userCinData['مكانها']['topPlusHeight'] = $bounding_boxesFront[$i]['height'] + $bounding_boxesFront[$i]['top'];
                    $userCinData['مكانها']['data'] = '';
                    break;
            }
        }


        for ($i = 0;
             $i < sizeof($bounding_boxesBack);
             $i++) {
            switch ($bounding_boxesBack[$i]['text']) {
                case 'المهنة':
                    $userCinData['المهنة']['top'] = $bounding_boxesBack[$i]['top'];
                    $userCinData['المهنة']['topPlusHeight'] = $bounding_boxesBack[$i]['height'] + $bounding_boxesBack[$i]['top'];
                    $userCinData['المهنة']['data'] = '';
                    break;
                case 'الأم':
                    $userCinData['الأم']['top'] = $bounding_boxesBack[$i]['top'];
                    $userCinData['الأم']['topPlusHeight'] = $bounding_boxesBack[$i]['height'] + $bounding_boxesBack[$i]['top'];
                    $userCinData['الأم']['data'] = '';
                    break;
                case 'العنوان':
                    $userCinData['العنوان']['top'] = $bounding_boxesBack[$i]['top'];
                    $userCinData['العنوان']['topPlusHeight'] = $bounding_boxesBack[$i]['height'] + $bounding_boxesBack[$i]['top'];
                    $userCinData['العنوان']['data'] = '';
                    break;
                case 'في':
                    $userCinData['في']['top'] = $bounding_boxesBack[$i]['top'];
                    $userCinData['في']['topPlusHeight'] = $bounding_boxesBack[$i]['height'] + $bounding_boxesBack[$i]['top'];
                    $userCinData['في']['data'] = '';
                    break;
            }
        }


        for ($i = 0; $i < sizeof($bounding_boxesFront); $i++) {
            if ($bounding_boxesFront[$i]['text'] !== 'اللقب' && $bounding_boxesFront[$i]['top'] < $userCinData['اللقب']['topPlusHeight'] && $bounding_boxesFront[$i]['top'] >= $userCinData['اللقب']['top']) {
                $userCinData['اللقب']['data'] = $userCinData['اللقب']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            } else if ($bounding_boxesFront[$i]['text'] !== 'الاسم' && $bounding_boxesFront[$i]['top'] < $userCinData['الاسم']['topPlusHeight'] && $bounding_boxesFront[$i]['top'] >= $userCinData['الاسم']['top']) {
                $userCinData['الاسم']['data'] = $userCinData['الاسم']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            } else if ($bounding_boxesFront[$i]['top'] <= $userCinData['بن']['topPlusHeight'] && $bounding_boxesFront[$i]['top'] >= $userCinData['بن']['top']) {
                $userCinData['بن']['data'] = $userCinData['بن']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            } else if ($bounding_boxesFront[$i]['text'] !== 'الولادة' && $bounding_boxesFront[$i]['top'] < $userCinData['تاريخ الولادة']['topPlusHeight'] && $bounding_boxesFront[$i]['top'] >= $userCinData['تاريخ الولادة']['top']) {
                $userCinData['تاريخ الولادة']['data'] = $userCinData['تاريخ الولادة']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            } else if ($bounding_boxesFront[$i]['text'] !== 'مكانها' && $bounding_boxesFront[$i]['top'] < $userCinData['مكانها']['topPlusHeight'] && $bounding_boxesFront[$i]['top'] >= $userCinData['مكانها']['top']) {
                $userCinData['مكانها']['data'] = $userCinData['مكانها']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            } else if (intval($bounding_boxesFront[$i]['text']) > 9999999 && is_numeric($bounding_boxesFront[$i]['text'])) {
                $userCinData['cart id']['data'] = $userCinData['مكانها']['data'] . ' ' . $bounding_boxesFront[$i]['text'];
            }
        }


        for ($i = 0; $i < sizeof($bounding_boxesBack); $i++) {
            if ($bounding_boxesBack[$i]['text'] !== 'المهنة' && $bounding_boxesBack[$i]['top'] < $userCinData['المهنة']['topPlusHeight'] && $bounding_boxesBack[$i]['top'] >= $userCinData['المهنة']['top']) {
                $userCinData['المهنة']['data'] = $userCinData['المهنة']['data'] . ' ' . $bounding_boxesBack[$i]['text'];
            } else if ($bounding_boxesBack[$i]['text'] !== 'الأم' && $bounding_boxesBack[$i]['top'] < $userCinData['الأم']['topPlusHeight'] && $bounding_boxesBack[$i]['top'] >= $userCinData['الأم']['top']) {
                $userCinData['الأم']['data'] = $userCinData['الأم']['data'] . ' ' . $bounding_boxesBack[$i]['text'];
            } else if ($bounding_boxesBack[$i]['text'] !== 'العنوان' && $bounding_boxesBack[$i]['top'] < $userCinData['العنوان']['topPlusHeight'] && $bounding_boxesBack[$i]['top'] >= $userCinData['العنوان']['top']) {
                $userCinData['العنوان']['data'] = $userCinData['العنوان']['data'] . ' ' . $bounding_boxesBack[$i]['text'];
            } else if ($bounding_boxesBack[$i]['text'] !== 'في' && $bounding_boxesBack[$i]['top'] < $userCinData['في']['topPlusHeight'] && $bounding_boxesBack[$i]['top'] >= $userCinData['في']['top']) {
                $userCinData['في']['data'] = $userCinData['في']['data'] . ' ' . $bounding_boxesBack[$i]['text'];
            }
        }


        $userCinData['تاريخ الولادة']['data'] = trim($userCinData['تاريخ الولادة']['data']);
        $userCinData['في']['data'] = trim($userCinData['في']['data']);
        $date = explode(" ", $userCinData['تاريخ الولادة']['data']);
        $date2 = explode(" ", $userCinData['في']['data']);

        for ($i = 0; $i < sizeof($date); $i++) {
            if (is_numeric($date[$i]) && intval($date[$i]) < 100)
                $day = $date[$i];
            elseif (is_numeric($date[$i]))
                $year = $date[$i];
            else
                $arabicMonth = $date[$i];
        }
        for ($i = 0; $i < sizeof($date2); $i++) {
            if (is_numeric($date2[$i]) && intval($date2[$i]) < 100)
                $day2 = $date2[$i];
            elseif (is_numeric($date2[$i]))
                $year2 = $date2[$i];
            else
                $arabicMonth2 = $date2[$i];
        }

        $englishMonth = [
            'جانفي' => 'January',
            'فيفري' => 'February',
            'مارس' => 'March',
            'أفريل' => 'April',
            'ماي' => 'May',
            'جوان' => 'June',
            'جويلية' => 'July',
            'أوت' => 'August',
            'سبتمبر' => 'September',
            'أكتوبر' => 'October',
            'نوفمبر' => 'November',
            'ديسمبر' => 'December',
        ];

        $englishMonthName = $englishMonth[$arabicMonth];
        $englishMonthName2 = $englishMonth[$arabicMonth2];

        $dateString = $day . '' . $englishMonthName . ' ' . $year;
        $dateString2 = $day2 . '' . $englishMonthName2 . ' ' . $year2;
        $userCinData['تاريخ الولادة']['data'] = date('m-d-Y', strtotime($dateString));
        $userCinData['في']['data'] = date('m-d-Y', strtotime($dateString2));


        dump($userCinData);
        $modifiedJsonString = json_encode($userCinData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents('../../files/usersJsonFiles/' . $filePathFrontCin . $filePathBackCin . '.json', $modifiedJsonString);
    }


    function getOcrResult($path, $fileName): string
    {
        return $this->Http('get-OCR_result?path=' . $path . '&fileName=' . $fileName);
    }


    private
    function getAllDesc($images_url): void
    {
        $result = [];
        for ($i = 0; $i < sizeof($images_url); $i++) {
            $result[] = $this->generateImageDescription($images_url[$i]);
        }
        $this->aiDataHolder->setDescriptions($result);
    }


    private
    function compareDescWithTitleAndCategory($descriptions, $obj): void
    {
        $result1 = [];
        $result2 = [];
        for ($i = 0; $i < sizeof($descriptions); $i++) {
            $result1[] = $this->getTitleValidation($descriptions[$i], $obj['title']);
            $result2[] = $this->getCategoryValidation($descriptions[$i], $obj['category']);
        }
        $this->aiDataHolder->setTitleValidation($result1);
        $this->aiDataHolder->setCategoryValidation($result2);

    }


    private
    function getTitleValidation($desc, $title): string
    {
        return $this->Http('get-title_validation?desc=' . $desc . '&title=' . $title);
    }

    private
    function getCategoryValidation($desc, $category): string
    {
        return $this->Http('get-category_validation?desc=' . $desc . '&category=' . $category);
    }

    private
    function generateImageDescription($image_url): string
    {
        $absolute_path = 'C:\Users\omar salhi\Desktop\PIDEV\citiezenHub_webapp\public\usersImg\\' . $image_url;
        return $this->Http('get-product_image_descreption?image_url=' . $absolute_path);
    }


    private
    function Http($url): string
    {
        $client = HttpClient::create();
        $response = $client->request('POST', 'http://127.0.0.1:5000/' . $url);
        $substringsToRemove = ['\"', '""\\', '"\n', '"', '\n'];
        $content = str_replace($substringsToRemove, "", $response->getContent());
        return $content;
    }


}