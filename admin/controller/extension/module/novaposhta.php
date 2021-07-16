<?php

class ControllerExtensionModuleNovaposhta extends Controller
{
    const API_URL = 'http://api.novaposhta.ua/v2.0/json/';

    const API_KEY = '833137cf02d3afcd62d64b568d5b8f4e';

    private $client;

    public function index()
    {
        $this->load->model('extension/module/cron');

        if (class_exists('\GuzzleHttp\Client')) {
            $this->client = new \GuzzleHttp\Client;
        }

        $this->runJobs();
    }

    private function runJobs()
    {
        var_dump($this->client);
        die;
    }
}
