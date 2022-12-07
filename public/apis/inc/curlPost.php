<?php

class CurlPost {
    private $url;

    /**
     * @param string $url     Request URL
     * @param array  $options cURL options
     */
    public function __construct($url)
    {
        $this->url = $url;
    }

    /**
     * Get the response
     * @return string
     * @throws \RuntimeException On cURL error
     */
    public function __invoke(array $posts)
    {
        $verbose = fopen('php://temp', "rw+");
        $postvars = '';
        foreach ($posts as $key => $value) {
            $postvars .= $key . "=" . $value . "&";
        }

        $ch = curl_init();
        \curl_setopt($ch, CURLOPT_URL, $this->url);

        \curl_setopt($ch, CURLOPT_POST, 1);
        \curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        \curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($ch, \CURLOPT_POSTFIELDS, $postvars);
        \curl_setopt($ch, CURLOPT_VERBOSE, true);
        \curl_setopt($ch, CURLOPT_STDERR, $verbose);

        $response = \curl_exec($ch);
        $error    = \curl_error($ch);
        $errno    = \curl_errno($ch);

        rewind($verbose);
        $verboseLog = stream_get_contents($verbose);
//        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

        if (\is_resource($ch)) {
            \curl_close($ch);
        }

        if (0 !== $errno) {
            throw new \RuntimeException($error, $errno);
        }

        return $response;
    }
}

