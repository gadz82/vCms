<?php
namespace apps\site\library\helpers;
class VideoHelper
{
    private $url;
    private $autoplay;

    function __construct($url, $autoplay = 0)
    {
        $this->url = $url;
        $this->autoplay = $autoplay;
    }

    public function render_embed()
    {
        if ($this->is_youtube()) {
            return $this->render_youtube_player();
        } elseif ($this->is_vimeo()) {
            return $this->render_vimeo_player();
        } elseif ($this->is_bliptv()) {
            return $this->render_bliptv_player();
        }
    }

    private function is_youtube()
    {
        return strpos($this->url, 'youtube') !== false;
    }

    private function render_youtube_player()
    {
        return '<iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/' . $this->get_youtube_ref_from_url() . '?autoplay=' . $this->autoplay . '&rel=0" frameborder="0"></iframe>';
    }

    private function get_youtube_ref_from_url()
    {
        $last_bit = explode('v=', $this->url);
        $last_bit = explode('&', $last_bit[1]);
        return $last_bit[0];
    }

    private function is_vimeo()
    {
        return strpos($this->url, 'vimeo') !== false;
    }

    private function render_vimeo_player()
    {
        return '<iframe src="http://player.vimeo.com/video/' . $this->get_vimeo_ref_from_url() . '?autoplay=' . $this->autoplay . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
    }

    private function get_vimeo_ref_from_url()
    {
        $last_bit = explode('.com/', $this->url);
        $last_bit = explode('&', $last_bit[1]);
        return $last_bit[0];
    }

    private function is_bliptv()
    {
        return strpos($this->url, 'vimeo') !== false;
    }

    private function render_bliptv_player()
    {
        return '<iframe src="http://blip.tv/play/' . $this->get_bliptv_ref_from_url() . '.html?p=' . $this->autoplay . '" frameborder="0" allowfullscreen></iframe><embed type="application/x-shockwave-flash" src="http://a.blip.tv/api.swf#' . $this->get_bliptv_ref_from_url() . '" style="display:none"></embed>';
    }

    private function get_bliptv_ref_from_url()
    {
        $last_bit = explode('play/', $this->url);
        $last_bit = explode('.', $last_bit[1]);
        return $last_bit[0];
    }

    public function the_vimeo_img()
    {
        $hash = unserialize(file_get_contents('http://vimeo.com/api/v2/video/' . $this->get_vimeo_ref_from_url() . '.php'));
        echo '<img src="' . $hash[0]['thumbnail_medium'] . '">';
    }

    public function the_youtube_img()
    {
        return "<div class='youtube_thumb'><img src=\"http://img.youtube.com/vi/" . $this->get_youtube_ref_from_url() . "/0.jpg\"></div>";
    }
}