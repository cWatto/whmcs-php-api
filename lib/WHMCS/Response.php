<?php


namespace WHMCS;


class Response {
    public $success;
    public $errorMessage;
    public $payload;

    public function __construct(array $apiResponse) {
        $this->success = ($apiResponse['result'] ?? '') === 'success';
        if( !$this->success ){
            $this->errorMessage = $apiResponse['message'] ?? 'An unexpected error occurred';
        }
        $this->payload = $apiResponse;
    }

    /**
     * Return an array item and it's key using "dot" notation
     * @param string $searchStr
     * @param string|null $default
     * @return array
     */
    public function find(string $searchStr, string $default = null) {
        $keys = explode('.', $searchStr);

        $itemKey = '';
        $item = $this->payload;
        foreach($keys as $key){
            $itemKey = $key;
            $item = $item[$key] ?? $default;
        }
        return $item;
    }

    /**
     * Returns an array of items from the response payload, from an array of given keys and options
     * If required option is true and default is not set, the whole function will return false.
     *
     * Example:
     * $this->pull([
     * 'result',
     * 'domains.domain.0',
     * 'userid' => [required: true, default: 'default!']
     * ]);
     *
     * @param array $list
     * @return array|false
     */
    public function pull(array $list){
        $items = [];
        foreach($list as $key => $value){
            $isRequired = false;
            $default = null;
            if( is_array($value) ){
                $isRequired = $value['required'] ?? false;
                $default = $value['default'] ?? null;
            }else{
                $key = $value;
            }

            $itemValue = $this->find($key, $default);
            $itemKey = strpos($key, '.') === false ? $key : substr($key, strpos($key, '.')+1, strlen($key));

            if( $isRequired && !$itemValue ){
                $items = false;
                break;
            }

            $items[$itemKey] = $itemValue;
        }
        return $items;
    }
}