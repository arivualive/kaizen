<?php

class Curl
{
    private $ch;
    public $base_url;
    private $url;
    private $post_data;
    private $debug;

    public function __construct($base_url, $debug = false)
    {
        $this->base_url = $base_url;
        $this->debug = $debug;
    }

    public function send($data)
    {
        if (isset($data['repository'])) {
            $data['module'] = 'default';
        } else {
            $data['module'] = 'sql';
        }

        $this->post_data = $data;
        $this->url = $this->base_url . $data['module'] . '.php';

        return $this->result();
    }

    private function exec()
    {
        $this->ch = curl_init();
        $this->options();

        return curl_exec($this->ch);
    }

    private function options()
    {
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($this->post_data));

        if ($this->debug === false) {
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($this->ch, CURLOPT_HEADER, true);
        }
    }

    public function result()
    {
        $response = $this->exec();
        $header_size = curl_getinfo($this->ch, CURLINFO_HEADER_SIZE);
        $header = substr($response, 0, $header_size);
        $body = substr($response, $header_size);

        return json_decode($body, true);
    }

    public function __destruct()
    {
        $this->close();
    }

    private function close()
    {
        if ($this->ch) {
            curl_close($this->ch);
        }
    }
}
