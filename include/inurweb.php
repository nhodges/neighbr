<?php

class GetWebObject
{
 var $host  = "";
 var $port  = "";
 var $path   = "";
 var $header = array();
 var $content = "";
 function GetWebObject($host, $port, $path)
 {
   $this->host = $host;
   $this->port = $port;
   $this->path = $path;
   $this->fetch();
 }
 
 function fetch()
 {
   $fp = fsockopen ($this->host, $this->port);
   
   if(!$fp)
   { die("Could not connect to host.");}
   
   $header_done=false;
   
   $request = "GET ".$this->path." HTTP/1.0\r\n";
   $request .= "User-Agent: Mozilla/4.0 (compatible; MSIE 5.5; Windows 98)\r\n";
   $request .= "Host: ".$this->host."\r\n";
   $request .= "Connection: Close\r\n\r\n";
   $return = '';
   
   fputs ($fp, $request);
   
   $line = fgets ($fp, 128);
   $this->header["status"] = $line;
   
   while (!feof($fp))
   {
     $line = fgets ( $fp, 256 );
     if($header_done)
     { $this->content .= $line;}
     else
     {
       if($line == "\r\n")
       { $header_done=true;}
       else
       {
         $data = explode(": ",$line);
         $this->header[$data[0]] = $data[1];
       }
     }
   }
   
   fclose ($fp);
 }
 
 function get_header()
 { return($this->header);}
 
 function get_content()
 { return($this->content);}
}  
?>