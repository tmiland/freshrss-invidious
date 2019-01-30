<?php

/**
 * Class InvidiousExtension
 *
 * Latest version can be found at https://github.com/tmiland/freshrss-invidious
 *
 * @author Tommy Miland
 */
class InvidiousExtension extends Minz_Extension
{
    /**
     * Video player domain
     * @var string
     */
    protected $domain = 'invidio.us';
    /**
     * Video player width
     * @var int
     */
    protected $width = 560;
    /**
     * Video player height
     * @var int
     */
    protected $height = 315;
    /**
     * Whether we display the original feed content
     * @var bool
     */
    protected $showContent = false;
    /**
     * Switch to redirect Youtube to Invidious
     * @var bool
     */
    protected $redirectYouTube = false;
    /**
     * Initialize this extension
     */
    public function init()
    {
        $this->registerHook('entry_before_display', array($this, 'embedInvidiousVideo'));
        $this->registerTranslates();
    }
    /**
     * Initializes the extension configuration, if the user context is available.
     * Do not call that in your extensions init() method, it can't be used there.
     */
    public function loadConfigValues()
    {
        if (!class_exists('FreshRSS_Context', false) || null === FreshRSS_Context::$user_conf) {
            return;
        }
        if (FreshRSS_Context::$user_conf->in_player_domain != '') {
            $this->domain = FreshRSS_Context::$user_conf->in_player_domain;
        }
        if (FreshRSS_Context::$user_conf->in_player_width != '') {
            $this->width = FreshRSS_Context::$user_conf->in_player_width;
        }
        if (FreshRSS_Context::$user_conf->in_player_height != '') {
            $this->height = FreshRSS_Context::$user_conf->in_player_height;
        }
        if (FreshRSS_Context::$user_conf->in_yt_redirect != '') {
            $this->redirectYouTube = (bool)FreshRSS_Context::$user_conf->in_yt_redirect;
        }
        if (FreshRSS_Context::$user_conf->in_show_content != '') {
            $this->showContent = (bool)FreshRSS_Context::$user_conf->in_show_content;
        }
    }
    /**
     * Returns domain name for the Invidious player iframe.
     * You have to call loadConfigValues() before this one, otherwise you get default values.
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->domain;
    }
    /**
     * Returns the width in pixel for the Invidious player iframe.
     * You have to call loadConfigValues() before this one, otherwise you get default values.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }
    /**
     * Returns the height in pixel for the Invidious player iframe.
     * You have to call loadConfigValues() before this one, otherwise you get default values.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }
    /**
     * Returns whether this extensions displays the content of the Invidious feed.
     * You have to call loadConfigValues() before this one, otherwise you get default values.
     *
     * @return bool
     */
    public function isShowContent()
    {
        return $this->showContent;
    }
    /**
     * Inserts the Invidious video iframe into the content of an entry, if the entries link points to a Invidious watch URL.
     *
     * @param FreshRSS_Entry $entry
     * @return mixed
     */
    public function embedInvidiousVideo($entry)
    {
        $html = $_POST['html'] ?? ''; // Fix for "PHP Notice: Undefined variable: html" Ref: https://stackoverflow.com/a/4261200
        $link = $entry->link();

        $this->loadConfigValues();

        if (stripos($entry->content(), '<iframe class="invidious-plugin-video"') !== false) {
            return $entry;
        }
        if (stripos($link, ''.$this->domain.'/watch?v=') !== false) {
            $html = $this->getIFrameForLink($link);
        }
        if (stripos($link, 'www.youtube.com/watch?v=') !== false && ($this->redirectYouTube)) { //YouTube
            $html = $this->getYouTubeIFrameForLink($link);
        }
        if ($this->showContent) {
            $html .= $entry->content();
        }

        $entry->_content($html);

        return $entry;
    }
    /**
     * Returns an HTML <iframe> for a given Invidious watch URL (invidio.us/watch?v=)
     *
     * @param string $link
     * @return string
     */
    public function getIFrameForLink($link)
    {

        $url = str_replace('//'.$this->domain.'/watch?v=', '//'.$this->domain.'/embed/', $link);
        $url = str_replace('http://', 'https://', $url);

        $html = $this->getIFrameHtml($url);

        return $html;
    }
    /**
    * Returns an HTML <iframe> for a given YouTube watch URL
    *
    * @param string $link
    * @return string
    */
    public function getYouTubeIFrameForLink($link)
    {

      $url = str_replace('//www.youtube.com/watch?v=', '//'.$this->domain.'/embed/', $link);
      $url = str_replace('http://', 'https://', $url);

      $html = $this->getIFrameHtml($url);

      return $html;
    }
    /**
     * Returns an HTML <iframe> for a given URL for the configured width and height.
     *
     * @param string $url
     * @return string
     */
    public function getIFrameHtml($url)
    {
        return '<iframe class="invidious-plugin-video"
                style="height: ' . $this->height . 'px; width: ' . $this->width . 'px;"
                width="' . $this->width . '"
                height="' . $this->height . '"
                src="' . $url . '"
                frameborder="0"
                allowfullscreen></iframe>';
    }
    /**
     * Saves the user settings for this extension.
     */
    public function handleConfigureAction()
    {
        $this->loadConfigValues();

        if (Minz_Request::isPost()) {
            FreshRSS_Context::$user_conf->in_player_domain = (string)Minz_Request::param('in_domain', '');
            FreshRSS_Context::$user_conf->in_player_height = (int)Minz_Request::param('in_height', '');
            FreshRSS_Context::$user_conf->in_player_width = (int)Minz_Request::param('in_width', '');
            FreshRSS_Context::$user_conf->in_show_content = (int)Minz_Request::param('in_show_content', 0);
            FreshRSS_Context::$user_conf->in_yt_redirect = (int)Minz_Request::param('in_yt_redirect', 0);
            FreshRSS_Context::$user_conf->save();
        }
    }
}
