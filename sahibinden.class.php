<?php
/**
 * Class Sahibinden
 * @author Tayfun Erbilen
 * @blog http://www.erbilen.net
 * @mail tayfunerbilen@gmail.com
 * @date 14.2.2014
 * @update ulusanyazilim@gmail.com
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
    public static function Kategori( $url = NULL )
    {
        if ( $url != NULL ) {
            $duzen='@<div id="searchCategoryContainer"(.*?)<\/div>@';
			$ac = self::Curl("http://www.sahibinden.com/".$url);
			preg_match($duzen,$ac,$sonuc);
			$duzen='@<li class="c(.*?)<a href="/(.*?)">(.*?)<\/a>(.*?)<span>\((.*?)\)<\/span>(.*?)li>@';   
			preg_match_all($duzen,$sonuc[0],$sonuc);
			foreach($sonuc[3] as $anahtar=>$deger){
				self::$data[]=array(
					"baslik"=>$deger,
					"sef" => $sonuc[2][$anahtar],  
					"ilan" => $sonuc[5][$anahtar],  
					"url"=>"http://www.sahibinden.com/".$sonuc[2][$anahtar]
				);
			}
        }else{
            $ac = self::Curl( 'http://www.sahibinden.com/' );
			$duzen='/<(.*?)mainCategory"(.*?)href="\/(.*?)">(.*?)<\/a>(.*?)<span(.*?)\((.*?)\)<\/span>/';
            preg_match_all($duzen,$ac,$sonuc);
            foreach ($sonuc[4] as $anahtar=>$deger){
				$sef=str_replace("kategori/","",$sonuc[3][$anahtar]);
				self::$data[ ]=array(
					"baslik"=>$deger,
					"sef" => $sef,  
					"ilan" => $sonuc[7][$anahtar],  
					"url"=>"http://www.sahibinden.com/".$sef
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
        $open = self::Curl( 'http://www.sahibinden.com/' . $kategoriLink . $page );
        preg_match_all( '/<tr class="searchResultsItem(.*?)">(.*?)<\/tr>/', $open, $result );
        foreach ( $result[ 2 ] as $detay ) {
            preg_match( '/<img src="(.*?)" alt="(.*?)" title="(.*?)"\/>/', $detay, $image );
            preg_match( '/<a class="classifiedTitle" href="(.*?)">(.*?)<\/a>/', $detay, $title );
            $items[ ] = array (
                'image' => $image[ 1 ],
                'title' => self::replaceSpace($image[ 3 ] ? $image[ 3 ] : trim( $title[ 2 ] )),
                'url' => 'http://www.sahibinden.com' . $title[ 1 ]
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
            preg_match_all( '/<li>                        <img src="(.*?)" data-source="(.*?)" alt="(.*?)"\/>                    <\/li>/', $open, $imgs );
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

            preg_match_all('/<a href="(.*?)">(.*?)<\/a>/', $extras, $addrs);
            $address = array(
                'il' => $addrs[2][0],
                'ilce' => $addrs[2][1],
                'mahalle' => $addrs[2][2]
            );

            // username
            preg_match('/<h5>(.*?)<\/h5>/', $open, $username);
            $username = $username[1];

            // contact info
            preg_match('/<ul class="userContactInfo">(.*?)<\/ul>/', $open, $contact_info);
            $contact_info = self::replaceSpace($contact_info[1]);
            preg_match_all('/<li> <strong>(.*?)<\/strong> <span>(.*?)<\/span> <\/li>/', $contact_info, $contact);

            foreach ( $contact[2] as $index => $val ){
                $contacts[$contact[1][$index]] = $val;
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
    private static function replaceSpace( $string )
    {
        $string = preg_replace( "/\s+/", " ", $string );
        $string = trim( $string );
        return $string;
    }

    /**
	 * Uzaktan site içeriğini alır.
	 *
     * @param $url
     * @return string
     */
    private static function Curl($url){
        $ch=curl_init($url);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true); 
		$icerik=curl_exec($ch);
		curl_close($ch);
		return str_replace(array("\n"),"",$icerik);
    }

}
