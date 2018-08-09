<?php

/**
 * Class Sahibinden
 * @author Tayfun Erbilen
 * @blog http://www.erbilen.net
 * @mail tayfunerbilen@gmail.com
 * @date 14.2.2014
 * @update 9.8.2018
 * @updater_mail facfur3@gmail.com
 */
class Sahibinden
{

    static $data = array ();

    /**
     * Tüm Kategorileri Listelemek İçin Kullanılır
     *
     * @param null $url
     * @return array
     */
    static function Kategori( $url = NULL )
    {
        if ( $url != NULL ) {
            $open = self::Curl( 'https://www.sahibinden.com/alt-kategori/' . $url );
            preg_match_all('@<li>(.*?)<a href="/(.*?)">(.*?)</a>(.*?)<span>(.*?)</span>(.*?)</li>@si',$open, $result);
            unset($result[2][0]);unset($result[3][0]);unset($result[5][0]);
            for ($i=0; $i <count($result[2]) ; $i++) { 
                self::$data[ ] = array (
                    'title' => $result[3][$i],
                    'icerik' => trim($result[5][$i]),
                    'uri' => trim($result[ 2 ][ $i ]),
                    'url' => 'https://www.sahibinden.com/' . $result[ 2 ][ $i ]
                );
            }
        }

        else {
            $open = self::Curl( 'https://www.sahibinden.com/' );
            preg_match_all( '@<li class="">(.*?)<a href="/kategori/(.*?)">(.*?)</a>(.*?)<span>((.*?))(.*?)</span>(.*?)</li>@si', $open, $result );
            foreach ( $result[ 2 ] as $key => $val ) {
                self::$data[ ] = array (
                    'title' => trim($result[3][$key]),
                    'icerik' => trim($result[7][$key]),
                    'uri' => str_replace( '/kategori/', '', $result[ 2 ][ $key ] ),
                    'url' => 'https://www.sahibinden.com/kategori/' . $result[ 2 ][ $key ]
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
    static function Liste( $kategoriLink, $sayfa = '0' )
    {
        $items = array ();
        $page = '?pagingOffset=' . $sayfa;
        $open = self::Curl('https://www.sahibinden.com/'.$kategoriLink .$page );
        preg_match_all( '@<tr data-id="(.*?)" class="searchResultsItem(.*?)">(.*?)</tr>@si', $open, $result );
        foreach ( $result[ 2 ] as $detay ) {
            preg_match( '@<img src="(.*?)" alt="(.*?)" title="(.*?)"/>@si', $open, $image );
            preg_match( '/<a class="classifiedTitle" href="(.*?)">(.*?)<\/a>/', $open, $title );
            $items[ ] = array (
                'image' => $image[ 1 ],
                'title' => self::replaceSpace($image[ 3 ] ? $image[ 3 ] : trim( $title[ 2 ] )),
                'url' => 'https://www.sahibinden.com' . $title[ 1 ]
            );
        }
        return $items;
    }

    /**
     * İlan detaylarını listeler.
     *
     * @param null $url
     * @return array
     */
    static function Detay( $url = NULL )
    {
        if ( $url != NULL ) {

            $open = self::Curl( $url );

            // title
            preg_match_all('/<div class="classifiedDetailTitle">    <h1>(.*?)<\/h1>/', $open, $titles);
            $title = $titles[1][0];

            // images
            preg_match_all( '@<img src="(.*?)" data-src="(.*?)" alt="(.*?)"/>@si', $open, $imgs );
            foreach ( $imgs[ 1 ] as $index => $val ) {
                $images[ ] = array (
                    'thumb' => $val,
                    'big' => $imgs[ 2 ][ $index ]
                );
            }

            // açıklama
            preg_match_all( '/<div id="classifiedDescription" class="uiBoxContainer">(.*?)<\/div>/', $open, $desc );
            $description = array (
                'html' => self::replaceSpace($desc[ 1 ][ 0 ]),
                'no_html' => self::replaceSpace(strip_tags( $desc[ 1 ][ 0 ] ))
            );

            // genel özellikler
            preg_match_all( '/<ul class="classifiedInfoList">(.*?)<\/ul>/', $open, $propertie );
            $prop = self::replaceSpace( $propertie[ 1 ][ 0 ] );
            preg_match_all( '/<li> <strong>(.*?)<\/strong>(.*?)<span(.*?)>(.*?)<\/span> <\/li>/', $prop, $p );
            foreach ( $p[ 1 ] as $index => $val ) {
                $properties[ trim( $val ) ] = str_replace( '&nbsp;', '', trim( $p[ 4 ][ $index ] ) );
            }

            // tüm özellikleri
            preg_match( '/<div class="uiBoxContainer classifiedDescription" id="classifiedProperties">(.*?)<\/div>/', $open, $allProperties );
            $allPropertiesString = self::replaceSpace( $allProperties[ 1 ] );
            preg_match_all( '/<h3>(.*?)<\/h3>/', $allPropertiesString, $propertiesTitles );
            preg_match_all( '/<ul>(.*?)<\/ul>/', $allPropertiesString, $propertiesResults );
            foreach ( $propertiesResults[ 1 ] as $index => $val ) {
                preg_match_all( '/<li class="(.*?)">(.*?)<\/li>/', $val, $result );
                foreach ( $result[ 1 ] as $index2 => $selected ) {
                    $props[ $propertiesTitles[ 1 ][ $index ] ][ ] = array ( $result[ 2 ][ $index2 ], $selected );
                }
            }

            // price
            preg_match('/<div class="classifiedInfo">(.*?)<\/div>/', $open, $extra);
            $extras = self::replaceSpace($extra[1]);
            preg_match('/<h3>(.*?)<\/h3>/', $extras, $price);
            $price = trim($price[1]);

            preg_match_all('@<a href="/(.*?)">(.*?)</a>@si', $open, $addrs);
            $address = array(
                'il' => trim($addrs[2][0]),
                'ilce' => trim($addrs[2][1]),
                'mahalle' => trim($addrs[2][2])
            );

            // username
            preg_match('/<h5>(.*?)<\/h5>/', $open, $username);
            $username = $username[1];

            // contact info
            preg_match('/<ul id="phoneInfoPart" class="userContactInfo">(.*?)<\/ul>/', $open, $contact_info);
            $contact_info = self::replaceSpace($contact_info[1]);
            preg_match_all('@<strong(.*?)>(.*?)</strong>(.*?)<span class="(.*?)">(.*?)</span>@si', $contact_info, $contact);
            foreach ( $contact[5] as $index => $val ){
                $contacts[$contact[2][$index]] = $val;
            }
            $data = array(
                'title' => $title,
                'images' => $images,
                'address' => $address,
                'description' => $description,
                'properties' => $properties,
                'all_properties' => $props,
                'price' => $price,
                'user' => array(
                    'name' => $username,
                    'contact' => $contacts
                )
            );

            return $data;

        }
    }

    /**
     * Gereksiz boşlukları temizler.
     *
     * @param $string
     * @return string
     */
    private function replaceSpace( $string )
    {
        $string = preg_replace( "/\s+/", " ", $string );
        $string = trim( $string );
        return $string;
    }

    /**
     * @param $url
     * @param null $proxy
     * @return mixed
     */
    private function Curl( $url, $proxy = NULL )
    {
        $options = array ( CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HEADER => false,
            CURLOPT_ENCODING => "",
            CURLOPT_AUTOREFERER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYPEER => false
        );

        $ch = curl_init("$url");
        curl_setopt_array( $ch, $options );
        $content = curl_exec( $ch );
        $err = curl_errno( $ch );
        $errmsg = curl_error( $ch );
        $header = curl_getinfo( $ch );



        curl_close( $ch );

        $header[ 'errno' ] = $err;
        $header[ 'errmsg' ] = $errmsg;
        $header[ 'content' ] = $content;

        return str_replace( array ( "\n", "\r", "\t" ), NULL, $header[ 'content' ] );
    }

}
