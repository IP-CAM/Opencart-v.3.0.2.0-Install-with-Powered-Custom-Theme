<?php

class ControllerExtensionModuleNovaposhta extends Controller
{
    const API_URL = 'http://api.novaposhta.ua/v2.0/json/';

    const API_KEY = '833137cf02d3afcd62d64b568d5b8f4e';

    private $client;

    public function index()
    {
        $this->load->model('extension/module/cron');

        $this->client = new Omaeurl;

        if (($this->request->server['REQUEST_METHOD'] == 'GET')) {
			if (isset($this->request->get['action'])) {
                if ($this->request->get['action'] == 'allcities') {
                    die($this->getCities());
                }
            }
        }
        // $this->updateCities();

        die;
    }

    public function getCities()
    {
        $result = file_get_contents(DIR_UPLOAD . 'cities.json');

        if (empty($result)) {
            $this->updateCities();
            $result = $this->getCities();
        }

        return $result;
    }

    public function getStreets($cityRef)
    {
        $client = $this->client;
        $body = [
            'type' => 'json',
            'modelName' => 'Address',
            'calledMethod' => 'getStreet',
            'methodProperties' => [
                'CityRef' => $cityRef
            ],
            'apiKey' => self::API_KEY
        ];
        $response = $client->request(self::API_URL, [], $body);

        return $response;
    }

    private function updateCities()
    {
        $client = $this->client;
        $body = [
            'type' => 'json',
            'modelName' => 'Address',
            'calledMethod' => 'getCities',
            'apiKey' => self::API_KEY
        ];
        $response = $client->request(self::API_URL, [], $body);

        $this->putToFile('cities.json', $response);
    }

    private function putToFile($fileName, $content)
    {
        if (!empty($response['errors'])) {
            return false;
        }
        $content = json_encode(['data' => $content['data']]);

        file_put_contents(DIR_UPLOAD . $fileName, $content);

        chmod(DIR_UPLOAD . $fileName, 0777);
    }
}
