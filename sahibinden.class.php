<?php

/**
 * Class Sahibinden
 * @author Tayfun Erbilen
 * @blog http://www.erbilen.net
 * @mail tayfunerbilen@gmail.com
 * @date 14.2.2014
 */
class Sahibinden
{

    static $data = array();

    private function Curl($url, $proxy = NULL)
    {
        $options = array (CURLOPT_RETURNTRANSFER => true, // return web page
            CURLOPT_HEADER => false, // don't return headers
            //CURLOPT_FOLLOWLOCATION => true, // follow redirects
            CURLOPT_ENCODING => "", // handle compressed
            CURLOPT_USERAGENT => "test", // who am i
            CURLOPT_AUTOREFERER => true, // set referer on redirect
            CURLOPT_CONNECTTIMEOUT => 30, // timeout on connect
            CURLOPT_TIMEOUT => 30, // timeout on response
            CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
            CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
        );
        $ch = curl_init ( $url );
        curl_setopt_array ( $ch, $options );
        $content = curl_exec ( $ch );
        $err = curl_errno ( $ch );
        $errmsg = curl_error ( $ch );
        $header = curl_getinfo ( $ch );

        curl_close ( $ch );

        $header['errno'] = $err;
        $header['errmsg'] = $errmsg;
        $header['content'] = $content;

        return str_replace(array("\n", "\r", "\t"), NULL, $header['content']);
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
        } else {
            $open = self::Curl('http://www.sahibinden.com/');
            preg_match_all('/<a class="mainCategory" title="(.*?)" href="(.*?)">(.*?)<\/a>/', $open, $result);
            foreach ($result[3] as $key => $val) {
                self::$data[] = array(
                    'title' => $val,
                    'uri' => str_replace('/kategori/', '', $result[2][$key]),
                    'url' => 'http://www.sahibinden.com' . $result[2][$key]
                );
            }
        }
        return self::$data;
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
            $open = self::Curl($url);
            // devam edecek
        };
    }

}
