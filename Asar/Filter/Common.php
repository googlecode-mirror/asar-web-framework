<?php

abstract class Asar_Filter_Common {
    
    static function filterResponse(Asar_Response $response) {
        if ($response->getMimeType() == 'text/html') {
            $content = $response->getContent(). <<<DEBUGHEAD

<div id="asar_debug">
    <h1>Asar Debugging Information</h1>
    <table id="asar_debug_table">
DEBUGHEAD;
            $debuginfo = Asar::getDebugMessages();
            foreach ($debuginfo as $key => $message):
                if (is_array($message)) {
                    $message = Asar_Helper_Html::uList($message);
                } else {
                    $message = htmlentities($message);
                }
                $key = htmlentities($key);
                $content .= <<<DEBUG

        <tr>
            <th scope="row">$key</th>
            <td>$message</td>
        </tr>

DEBUG;
            endforeach;
            $response->setContent($content . "    </table>\n</div>\n</body>");
        }
        return $response;
    }
}