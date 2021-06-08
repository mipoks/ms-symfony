<?php


namespace App\Util;

use App\Entity\Person;
use App\Entity\Song;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use DOMDocument;
use Exception;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MusicGetter
{
    private $songRepository;

    public function __construct(SongRepository $songRepository)
    {
        $this->songRepository = $songRepository;
    }

    private function getDoc(string $search, int $count) : ?array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.deezer.com/search?q='.$search,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: deezerdevs-deezer.p.rapidapi.com",
                "x-rapidapi-key: 6d88d9b7edmsh9c0e25d90ba65e3p192a13jsn2003c64a93c7"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        if ($err) {
            return null;
        } else {
            return $this->parseResponse($response, $count);
        }
    }

    private function parseResponse(string $response, int $count): ?array{
        try {
            $songs = array();
            echo ("<script>console.log('from musicgetter: ".$response."');</script>");
            $array = json_decode($response, true);
            $tracks = null;
            try {
                $tracks = $array["tracks"]["data"];
            } catch (Exception $ex) {
                $tracks = $array["data"];
            }
            $num = 0;
            foreach ($tracks as $track) {
                $artistName = $track["artist"]["name"];
                $title = $track["title"];
                $link = $track["preview"];
                $id = (int)$track["id"];

                $songTemp = new Song();
                $songTemp->setId($id);
                $songTemp->setSongName($title . " – " . $artistName);
                $songTemp->setOriginalUrl($link);
                $songTemp->setUrl($link);

                echo ("<script>console.log('from musicgetter: ".json_encode($songTemp)."');</script>");

                array_push($songs, $songTemp);
                $num++;
                if ($num == $count)
                    break;
            }
            return $songs;
        } catch (Exception $ex) {
            return null;
        }
    }

    public function getWithSearch(int $count, string $search): ?array
    {
        return $this->getDoc(urlencode($search), $count);
    }

    public function get(int $count): ?array
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.deezer.com/chart',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: deezerdevs-deezer.p.rapidapi.com",
                "x-rapidapi-key: 6d88d9b7edmsh9c0e25d90ba65e3p192a13jsn2003c64a93c7"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);


        echo ("<script>console.log('from musicgetter: ".$response."');</script>");
        echo ("<script>console.log('from musicgetter: ".$err."');</script>");
        if ($err) {
            return null;
        } else {
            return $this->parseResponse($response, $count);
        }
    }

}