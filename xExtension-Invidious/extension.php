<?php

/**
 * Class InvidiousExtension
 *
 * Latest version can be found at https://github.com/tmiland/freshrss-invidious
 *
 * @author Kevin Papst
 */
class InvidiousExtension extends Minz_Extension
{
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

        if (FreshRSS_Context::$user_conf->in_player_width != '') {
            $this->width = FreshRSS_Context::$user_conf->in_player_width;
        }
        if (FreshRSS_Context::$user_conf->in_player_height != '') {
            $this->height = FreshRSS_Context::$user_conf->in_player_height;
        }
        if (FreshRSS_Context::$user_conf->in_show_content != '') {
            $this->showContent = (bool)FreshRSS_Context::$user_conf->in_show_content;
        }
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
        $link = $entry->link();

        if (preg_match('#^https?://invidio\.us/watch\?v=|/videos/watch/[0-9a-f-]{36}$#', $link) !== 1) {
            return $entry;
        }

        $this->loadConfigValues();

        if (stripos($entry->content(), '<iframe class="invidious-plugin-video"') !== false) {
            return $entry;
        }
        if (stripos($link, 'invidio.us/watch?v=') !== false) {
            $html = $this->getIFrameForLink($link);
        }
        if ($this->showContent) {
            $html .= $entry->content();
        }

        $entry->_content($html);

        return $entry;
    }

    /**
     * Returns an HTML <iframe> for a given Invidious watch URL (www.invidio.us/watch?v=)
     *
     * @param string $link
     * @return string
     */
    public function getIFrameForLink($link)
    {
        $domain = 'invidio.us';

        $url = str_replace('//invidio.us/watch?v=', '//'.$domain.'/embed/', $link);
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
            FreshRSS_Context::$user_conf->in_player_height = (int)Minz_Request::param('in_height', '');
            FreshRSS_Context::$user_conf->in_player_width = (int)Minz_Request::param('in_width', '');
            FreshRSS_Context::$user_conf->in_show_content = (int)Minz_Request::param('in_show_content', 0);
            FreshRSS_Context::$user_conf->save();
        }
    }
}
