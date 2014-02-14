<?php

class Sahibinden
{

    static $data = array();

    private function Curl($url, $proxy = NULL)
    {
        $options = Array(
            CURLOPT_RETURNTRANSFER => TRUE,
            // CURLOPT_FOLLOWLOCATION => TRUE,
            CURLOPT_REFERER => 'http://www.google.com/?q=Sahibinden #'.rand(0,9999999999),
            CURLOPT_CONNECTTIMEOUT => 120,
            CURLOPT_TIMEOUT => 120,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
            CURLOPT_URL => $url,
            CURLOPT_HTTPPROXYTUNNEL => 0,
            CURLOPT_PROXY => $proxy
        );
        $ch = curl_init();
        curl_setopt_array($ch, $options);
        $data = curl_exec($ch);
        curl_close($ch);
        return str_replace(array("\n", "\r", "\t"), NULL, $data);
    }

    /**
     * Tüm Kategorileri Listelemek İçin Kullanılır
     *
     * @param null $url
     * @return array
     */
    static function Kategori($url = NULL)
    {
        if ($url != NULL) {
            $open = self::Curl('http://www.sahibinden.com/alt-kategori/' . $url);
            preg_match_all('/<div> <a href="(.*?)">(.*?)<\/a> <span>\((.*?)\)<\/span> <\/div>/', $open, $result);
            foreach ($result[2] as $key => $val) {
                self::$data[] = array(
                    'title' => $val,
                    'uri' => trim(str_replace('/kategori/', '', $result[1][$key]), '/'),
                    'url' => 'http://www.sahibinden.com' . $result[1][$key]
                );
            }
            return self::$data;
        } else {
            $open = self::curl('http://www.sahibinden.com/');
            preg_match_all('/<a class="mainCategory" title="(.*?)" href="(.*?)">(.*?)<\/a>/', $open, $result);
            foreach ($result[3] as $key => $val) {
                self::$data[] = array(
                    'title' => $val,
                    'uri' => str_replace('/kategori/', '', $result[2][$key]),
                    'url' => 'http://www.sahibinden.com' . $result[2][$key]
                );
            }
            return self::$data;
        }
    }

    /**
     * Kategoriye ait ilanları listeler.
     *
     * @param $kategoriLink
     * @param string $sayfa
     * @return array
     */
    static function Liste($kategoriLink, $sayfa = '0')
    {
        $items = array();
        $page = '?pagingOffset=' . $sayfa;
        $open = self::Curl('http://www.sahibinden.com/' . $kategoriLink . $page);
        preg_match_all('/<tr class="searchResultsItem(.*?)">(.*?)<\/tr>/', $open, $result);
        foreach ($result[2] as $detay) {
            preg_match('/<img src="(.*?)" alt="(.*?)" title="(.*?)"\/>/', $detay, $image);
            preg_match('/<a class="classifiedTitle" href="(.*?)">(.*?)<\/a>/', $detay, $title);
            $items[] = array(
                'image' => $image[1],
                'title' => $image[3] ? $image[3] : trim($title[2]),
                'url' => 'http://www.sahibinden.com' . $title[1]
            );
        }
        return $items;
    }

    static function Detay($url = NULL)
    {
        if ($url != NULL) {
            $open = self::Curl($url, '193.203.220.15:8080');
            preg_match_all('/<title>(.*?)<\/title>/', $open, $result);
            print_r($result);
        };
    }

}
