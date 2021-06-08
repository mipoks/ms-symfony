<?php


namespace App\Services;


use App\Entity\Chat;
use App\Entity\ChatMessage;
use App\Entity\Person;
use App\Entity\Song;
use App\Form\SongText;
use App\Repository\ChatRepository;
use App\Repository\MessageRepository;
use App\Repository\PersonRepository;
use App\Repository\SongRepository;
use Doctrine\ORM\EntityManagerInterface;
use Httpful\Exception\ConnectionErrorException;
use Httpful\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SongTextService
{
    public $base_url;
    public $parameters;
    public $endpoint;
    private $api_key;

    public function __construct()
    {
        $this->api_key = 'de314ac2090e3c31bd66db2d9a75f235';
        $this->base_url = "http://api.musixmatch.com/ws/1.1/";
        $this->parameters = [
            'apikey' => $this->api_key,
        ];
    }

    public function getText(string $songName = null, string $artist = null): SongText
    {
        $songText = new SongText();
        $track = $this->getTheMostMatchingTrackId($artist, $songName)->results();

        try {
            $lyrics = $this->getLyrics($track->track_id)->results();
            $songText->setText(substr($lyrics->lyrics_body, 0, strlen($lyrics->lyrics_body) - 71));
            $songText->setName($track->track_name);
            $songText->setId($track->track_id);
        } catch (\Exception $ex) {
        }
        return $songText;
    }

    private function getTheMostMatchingTrackId(string $artist = null, string $title = null)
    {
        if (!is_null($artist)) {
            $this->parameters['q_artist'] = $artist;
        }
        if (!is_null($title)) {
            $this->parameters['q_track'] = $title;
        }
        $this->endpoint = "matcher.track.get";
        return $this;
    }


    private function getLyrics($track_id)
    {
        $this->endpoint = "track.lyrics.get";
        $this->parameters['track_id'] = $track_id;
        return $this;
    }

    private function results()
    {
        $request_url = $this->createRequestUrl();

        try {
            $response = Request::get($request_url)->expectsType("json")->send();
            return $this->formatApiResults($response);
        } catch (ConnectionErrorException $e) {
        }
        return null;
    }


    private function formatApiResults($result)
    {
        $raw = $result->raw_body;

        if ($this->endpoint == "matcher.track.get") {
            $track = $result->body->message->body->track;
            return $track;
        }

        if ($this->endpoint == "track.lyrics.get") {
            $lyrics = $result->body->message->body->lyrics;
            return $lyrics;
        }
        return null;
    }

    private function createRequestUrl()
    {
        $parameters = http_build_query($this->parameters);
        return "{$this->base_url}{$this->endpoint}?{$parameters}";
    }

}