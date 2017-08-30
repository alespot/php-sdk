<?php

namespace NextEvent\PHPSDK\Util;

/**
 * Booking widget embedding utility
 *
 * For integrating the booking process into a partner platform, a widget is
 * available. Widgets are referenced by Widget-Hash.
 *
 * You need to embed the Widget <code>/assets/js/widgetapi.js</code> into your
 * hostpage and use the <code>NextEventWidgetAPI</code> to interact with the widget.
 *
 * ### Embed Widget
 *
 * ```php
 * <?php
 * use NextEvent\PHPSDK\Client;
 *
 * $appUrl = 'https://myapp.nextevent.com';
 * $credentials = [...];
 * $widgetHash = 's67fa757df4a76s5';
 *
 * $client = new Client($appUrl, $credentials, $widgetHash);
 *
 * // get Widget
 * $widget = $client->getWidget();
 *
 * // embed Widget
 * echo $widget->generateEmbedCode($event_id);
 * ```
 *
 * ### NextEventWidgetAPI
 *
 * The <code>NextEventWidgetAPI</code> is a JavaSrcipt script, which you can use to
 * register Message Handlers. Therefore the script has to be included into the
 * hostpage.
 *
 * Use <code>NextEventWidgetAPI</code> for retrieving the <code>order_id</code> and send
 * it to your server.
 * Use <code>NextEventWidgetAPI</code> to show your own payment step on
 * <code>current_step</code> message
 *
 * ```html
 * <script src="/assets/js/widgetapi.js"></script>
 * ```
 *
 * When the script is included the NextEventWidgetAPI is available by <code>window
 * .NextEventWidgetAPI</code>
 *
 * ```js
 * function custom_handler(data) {
 * // do something with the data
 * }
 *
 * // handler can be added
 * window.NextEventWidgetAPI.addMessageHandler('current_step', custom_handler);
 * // and removed
 * window.NextEventWidgetAPI.removeMessageHandler('current_step', custom_handler);
 * ```
 *
 * ### Widget Messages
 *
 * #### current_step
 *
 * This message is send on changing routes.
 * <code>step</code> current step a path
 *
 * example data:
 * ```js
 * {
 * step: '/event/42'
 * }
 *
 * {
 * step: '/payment'
 * }
 *
 * {
 * step: '/checkout'
 * }
 * ```
 *
 * #### basket_update
 *
 * This message is send when the basket is updated
 * <code>order_id</code> id from current basket
 *
 * example data:
 * ```js
 * {
 * order_id: 5005
 * }
 * ```
 *
 * @package NextEvent\PHPSDK\Util
 */
class Widget
{
  /**
   * Widget-Hash
   *
   * @var string
   */
  protected $hash;
  /**
   * Url to the App
   *
   * @var string
   */
  protected $appUrl;


  /**
   * Widget constructor
   *
   * @internal
   * @param string $appUrl
   * @param string $hash
   */
  public function __construct($appUrl, $hash)
  {
    $this->appUrl = rtrim($appUrl, '/');
    $this->hash = $hash;
  }


  /**
   * Compose HTML to be embedded in the host website
   *
   * @param int $eventId ID of the event to book tickets for
   * @return string <script> tag loading the booking widget
   */
  public function generateEmbedCode($eventId = null)
  {
    $embedSrc = $this->generateEmbedSrc(
      $this->appUrl,
      $this->hash,
      $eventId
    );

    $code = '<script class="nextevent" type="text/javascript" src="' . htmlentities(
        $embedSrc
      ) . '"></script>';
    return $code;
  }


  /**
   * Generate embed src for the script tag
   *
   * @param string $appUrl
   * @param string $hash
   * @param string $eventId
   * @return string
   */
  protected function generateEmbedSrc($appUrl, $hash, $eventId)
  {
    $pos = strpos($appUrl, '://') + 3;
    $src = substr_replace($appUrl, 'widget-' . $hash . '-', $pos, 0);
    $src .= '/widget/embed/';
    $locale = '';
    if (Env::getVar('locale')) {
      $locale = '/' . substr(Env::getVar('locale'), 0, 2);
    }
    if ($eventId) {
      $src .= '#src=' . $locale . '/event/' . $eventId;
    } else if ($locale) {
      $src .= '#src=' . $locale;
    }
    return $src;
  }
}
