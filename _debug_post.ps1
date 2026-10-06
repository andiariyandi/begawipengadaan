BASE=http://begawipengadaan.test
jar=New-Object System.Net.CookieContainer
function Get-Csrf($url,$j){$r=[System.Net.HttpWebRequest]::Create($url);$r.CookieContainer=$j;$r.UserAgent=Mozilla/5.0;$r.Timeout=60000;$resp=$r.GetResponse();$rdr=New-Object System.IO.StreamReader($resp.GetResponseStream());$h=$rdr.ReadToEnd();$resp.Close();$m=[regex]::Match($h,name=[chr(34)]+csrf_test_name+[chr(34)]+[^>]+value=[chr(34)]+([^"+[chr(34)]+"]+)[chr(34)]+);if($m.Success){return $m.Groups[1].Value};return $null}
