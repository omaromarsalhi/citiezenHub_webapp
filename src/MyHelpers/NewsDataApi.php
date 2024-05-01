<?php
namespace App\MyHelpers;

use GuzzleHttp\Client;

class NewsDataApi {
    public function getNews() {
        $newsList = array();
        $apiKey = "pub_392294278cf250c575945041b139b558918a6";
        $apiUrl = "https://newsdata.io/api/1/news?apikey=" . $apiKey . "&country=tn&language=fr";

        $client = new Client();
        $response = $client->request('GET', $apiUrl);

        if ($response->getStatusCode() !== 200) {
            die('Erreur lors de la récupération des données: ' . $response->getStatusCode());
        }

        $data = json_decode($response->getBody(), true);

        if (isset($data['results'])) {
            foreach ($data['results'] as $article) {
                $news = array(
                    "sourceId" => $article["source_id"],
                    "date" => $article["pubDate"],
                    "title" => $article["title"],
                    "description" => $article["description"],
                    "imageUrl" => isset($article["image_url"]) ? $article["image_url"] : null,
                    "linkUrl" => $article["link"],
                    "sourceIcon" => isset($article["source_icon"]) ? $article["source_icon"] : null
                );
                array_push($newsList, $news);
            }
        }

        return $newsList;
    }
}

