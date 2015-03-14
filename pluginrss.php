<?php

/*
Plugin Name: Plugin Rss with thumbnails
Plugin URI: 
Description: Esse plugin é um feed-reader como outro qualquer, porém, ele apresenta também os thumbnails presentes no feed
Author: Rodrigo Morbach
Version: 1
*/

class PluginRssThumb extends WP_Widget
{
    function PluginRssThumb()
    {
        $widget_ops = array(
            'classname' => 'RssWithThumbnails',
            'description' => 'Apresenta Feed Rss com Thumbnails'
        );
        $this->WP_Widget('RssWithThumbnails', 'Rss With Thumbnail', $widget_ops);
    }
    
    function form($instance)
    {
        $instance = wp_parse_args((array) $instance, array(
            'quantidade' => '',
            'titulo' => '',
            'xml' => ''
        ));
        $title    = $instance['quantidade'];
        $titulo   = $instance['titulo'];
        $xml      = $instance['xml'];
?>
  <p><label for="<?php
        echo $this->get_field_id('quantidade');
?>">Quantidade de Feeds: <input class="widefat" id="<?php
        echo $this->get_field_id('quantidade');
?>" name="<?php
        echo $this->get_field_name('quantidade');
?>" type="text" value="<?php
        echo attribute_escape($title);
?>" /></label></p>
  <p><label for="<?php
        echo $this->get_field_id('titulo');
?>">Titulo (opcional): <input class="widefat" id="<?php
        echo $this->get_field_id('titulo');
?>" name="<?php
        echo $this->get_field_name('titulo');
?>" type="text" value="<?php
        echo attribute_escape($titulo);
?>" /></label></p>
  <p><label for="<?php
        echo $this->get_field_id('xml');
?>">Informe a URL do Feed: <input class="widefat" id="<?php
        echo $this->get_field_id('xml');
?>" name="<?php
        echo $this->get_field_name('xml');
?>" type="text" value="<?php
        echo attribute_escape($xml);
?>" /></label></p>
    <?php
    }
    
    function update($new_instance, $old_instance)
    {
        $instance               = $old_instance;
        $instance['quantidade'] = $new_instance['quantidade'];
        $instance['titulo']     = $new_instance['titulo'];
        $instance['xml']        = $new_instance['xml'];
        return $instance;
    }
    
    function widget($args, $instance)
    {
        extract($args, EXTR_SKIP);
        $titulo = $instance['titulo'];
        $xml    = $instance['xml'];
        echo $before_widget;
        $title = empty($instance['quantidade']) ? ' ' : apply_filters('widget_title', $instance['quantidade']);
        if (!empty($title))
            echo $before_title . $titulo . $after_title;
        if (empty($xml)) {
            echo 'Nenhum Feed Encontrado';
        } else {
            $xmlDoc = new DOMDocument();
            $xmlDoc->load($xml);
            $x = $xmlDoc->getElementsByTagName('item');
            for ($i = 0; $i < $instance['quantidade']; $i++) //Define quantas noticias aparecer o na pagina
                {
                $item_date  = $x->item($i)->getElementsByTagName('pubDate')->item(0)->childNodes->item(0)->nodeValue;
                $item_title = $x->item($i)->getElementsByTagName('title')->item(0)->childNodes->item(0)->nodeValue;
                $item_link  = $x->item($i)->getElementsByTagName('link')->item(0)->childNodes->item(0)->nodeValue;
                $item_desc  = $x->item($i)->getElementsByTagName('description')->item(0)->childNodes->item(0)->nodeValue;
                
                echo "<div class='noticia'>";
                echo ("<p><a href='" . $item_link . "' target='_blank'>" . $item_title . "</a>");
                $item_date = strftime("%d-%m-%Y %H:%M", strtotime($item_date));
                echo "<br /><b>" . $item_date . "</b>";
                echo "<div class='imagemrss'>";
                if (strstr($item_desc, 'img')) {
                    $item_desc = str_replace('<br />', '', $item_desc);
                    echo ($item_desc . "</div></p>");
                } else {
                    echo ("<a href='" . $item_link . "' target='_blank'><img src='' /></a>" . $item_desc . "</div></p>");
                }
                echo "</div>";
                
            }
        }
        echo $after_widget;
    }
    
}
add_action('widgets_init', create_function('', 'return register_widget("PluginRssThumb");'));
?>