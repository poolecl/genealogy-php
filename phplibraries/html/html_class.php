<?php
/**
 * html_class.php
 * 
 * @author Christopher Poole <poolecl@yahoo.ca>
 * @version .01
 * @package genealogy
 */

  /**
   * @author Christopher Poole <poolecl@yahoo.ca>
   * @version .01
   * @package genealogy
   */
  abstract class WebPageAbstract
  {

    public function getWebPage()
    {
      $output = "<html>\n";
      $output .= $this->getHeader();
      $output .= $this->getBody();
      $output .= "</html>\n";
      return $output;
    }

    public function getHeader()
    {
      $output = "<head>\n";
      $output .= $this->getJavascript();
      $output .= $this->getHeaderContent();
      $output .= "<link href=\"style.css\" rel=\"stylesheet\" type=\"text/css\" />";      
      $output .= "<title>";
      $output .= $this->getTitle();
      $output .= "</title>\n";
      $output .= "</head>\n";
      return $output;
    }

    public function getJavascript()
    {
      $functions = $this->getJavascriptFunctions();
      $output = '';
      if (isset($functions) && $functions != '')
      {
        $output = "<script type=\"text/javascript\" charset=\"utf-8\">\n";
        $output .= "//<![CDATA[\n";
        $output .= $functions;
        $output .= "\n//]]>\n";
        $output .= "</script>\n";
      }
        return $output;
    }

    public function getJavascriptFunctions()
    {
      return '';
    }
    
    public function getHeaderContent()
    {
      return '';
    }
    
    public abstract function getTitle();
    
    public function getBody()
    {
      $output = "<body>\n";
      $output .= $this->getBodyContent();
      $output .= "</body>\n";
      return $output;
    }

    public abstract function getBodyContent();
 }
?>