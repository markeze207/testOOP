<?php
class RestApi{
    /** @var modX $modx */
    public $modx = null;
    private $token;
    private $url;
    private $curl;
    /**
     * @param  string  $token
     * @return bool
     */
    public function __construct(modX $modx, string $token) {
        $this->modx = $modx;
        $this->token = $token;
        $this->url = $this->modx->getOption('api_url_bx24');
        $this->curl = curl_init();
        return true;
    }
    public function __destruct() {
        curl_close($this->curl);
    }
    /**
     * @param  string  $method
     * @param  array  $fields
     * @return array
     */
    public function get(string $method,array $fields = []){
        $result = [];
        if($this->url && $this->token){
            $fields['key'] = $this->token;
            $options = array(
                CURLOPT_URL => $this->url . $method . '?' . http_build_query($fields),
                CURLOPT_HEADER => false,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TCP_KEEPALIVE => 1,
                CURLOPT_TCP_KEEPIDLE => 60,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_TCP_KEEPINTVL => 10,
                CURLOPT_SSL_VERIFYPEER => false,
            );
            curl_setopt_array($this->curl, $options);
            $result = curl_exec($this->curl);
        }
        file_put_contents('test5235.txt', print_r($this->url . $method . '?' . http_build_query($fields), true));
        return json_decode($result, true);
    }
}