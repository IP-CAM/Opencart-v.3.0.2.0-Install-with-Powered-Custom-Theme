<?php

class ControllerExtensionModuleNovaposhta extends Controller
{
    const API_URL = 'http://api.novaposhta.ua/v2.0/json/';

    const API_KEY = '833137cf02d3afcd62d64b568d5b8f4e';

    private $client;

    public function index()
    {
        $this->load->model('extension/module/cron');

        $this->client = '';

        $this->runJobs();
    }

    private function runJobs()
    {
        $this->updateCities();
    }

    private function updateCities()
    {
        $headers = [
            'Accept' => 'application/json'
        ];
        $body = [
            'modelName' => 'Address',
            'calledMethod' => 'getCities',
            'apiKey' => self::API_KEY
        ];
        
        die;
    }
}
