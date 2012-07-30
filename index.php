<?php
/*
 *      This program is free software; you can redistribute it and/or modify
 *      it under the terms of the GNU General Public License as published by
 *      the Free Software Foundation; either version 2 of the License, or
 *      (at your option) any later version.
 *      
 *      This program is distributed in the hope that it will be useful,
 *      but WITHOUT ANY WARRANTY; without even the implied warranty of
 *      MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *      GNU General Public License for more details.
 *      
 *      You should have received a copy of the GNU General Public License
 *      along with this program; if not, write to the Free Software
 *      Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
 *      MA 02110-1301, USA.
 */

define('CACHE_FILE', dirname(__FILE__).'/cache.rss');

if ( is_file( CACHE_FILE ) && filemtime(CACHE_FILE) > time()-1800 ) {
	$str = file_get_contents( CACHE_FILE );
}
else {
	if( $html = @file_get_contents('http://eztv.it/') ) {
		$html = str_replace('&amp;','&',str_replace('  ','',str_replace(array("\n","\r","\t"),' ',$html)));
		$pattern = '|class="epinfo">(.*)</a> </td> <td align="center" class="forum_thread_post"><a href="(.*)" class="magnet"|U';
		
		if( preg_match_all( $pattern, $html, $out, PREG_PATTERN_ORDER ) ) {
			
			$str = <<<HEADER
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0">
<channel>
<title>eztv.it homepage shows RSS</title>
<link>http://eztv.it</link>
<description>eztv.it homepage shows RSS</description>
HEADER;
			
			foreach( $out[1] as $k => $v) {
				$link = htmlspecialchars($out[2][$k]);
				$txt = htmlspecialchars($v);
				$str .= <<<ITEM
<item>
<title>$txt</title>
<link>$link</link>
<description>$txt</description>
</item>

ITEM;
			}
			
			$str .= <<<FOOTER
</channel>
</rss>
FOOTER;
			file_put_contents( CACHE_FILE, $str);
		}	
	}
}
header("Content-Type: application/rss+xml");
die($str);
