<?php

abstract class Asar_Filter_Common {
    
    static function filterResponse(Asar_Response $response) {
        if ($response->getMimeType() == 'text/html' && Asar::MODE_DEVELOPMENT == Asar::getMode()) {
            
			$debug = <<<DEBUGHEAD

<div id="asar_debug">
    <h1>Asar Debugging Information</h1>
    <table id="asar_debug_table">
DEBUGHEAD;
            $debuginfo = Asar::getDebugMessages();
            if (is_array($debuginfo)):
            foreach ($debuginfo as $key => $message):
                if (is_array($message)) {
                    $message = Asar_Helper_Html::uList($message);
                } else {
                    $message = htmlentities($message);
                }
                $key = htmlentities($key);
                $debug .= <<<DEBUG

        <tr>
            <th scope="row">$key</th>
            <td>$message</td>
        </tr>
    </table>
</div>
</body>
DEBUG;
            endforeach;
            endif;
			$response->setContent(str_replace('</body>', $debug, $response->getContent()) );
        }
        return $response;

    }
    
    static function filterRequestTypeNegotiation(Asar_Request $request)
    {
        $path = $request->getPath();
        if (empty($path)) {
            $request->setPath('/');
        } else {
		    // Remove the string after the '?'
    		if (strpos($path, '?')) {
    			$path = substr($path, 0, strpos($path, '?'));
    		}

    		// Get the file extension
    		if (strrpos($path, '.') > 1) {
    		    // Remove the string before the last occurrence of the '/'
        		$fname = substr($path, strrpos($path, '/') + 1);
        		$type = substr($fname, strrpos($fname, '.') + 1);
        		$path = substr($path, 0, strrpos($fname, '.') + 1);
        		$request->setType($type);
        		$request->setPath($path);
        	}
    	}

    }
}