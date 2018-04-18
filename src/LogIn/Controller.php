<?php
/**
 * Created by PhpStorm.
 * User: Deus
 * Date: 18/04/2018
 * Time: 18:36
 */

namespace HeraldryEngine\LogIn;
use Exception;
use HeraldryEngine\DatabaseContainer;
use HeraldryEngine\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use UAParser\Parser;

class Controller
{
    /**
     * @var DatabaseContainer
     */
    public $db;
    /**
     * @var Request
     */
    public $request;
    /**
     * @var array
     */
    public $params;

    /**
     * Controller constructor.
     * @param DatabaseContainer $db
     * @param Request $request
     */
    public function __construct(DatabaseContainer $db, Request $request)
    {
        $this->db = $db;
        $this->request = $request;
        $this->params = [];
    }

    /**
     * @param callable $clock
     * @param int $lifetime
     * @param string $uname
     * @param string $pword
     * @return SecurityContext
     */
    public function authenticateUser($clock, $lifetime, $uname, $pword){
        $uname=strtolower($uname);
        /**
         * @var \mysqli_stmt $stmt
         */
        $stmt = $this->db->prepareQuery(
        /** @lang MySQL */
            "SELECT COUNT(*) FROM failureLogs ".
            "WHERE userName=? AND accessTime > (NOW() - INTERVAL 5 MINUTE);"
        );
        $stmt->bind_param("s", $uname);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_row();
        $attempts = $row[0];
        $stmt->close();
        if($attempts < 50){
            $stmt = $this->db->prepareQuery(
            /** @lang MySQL */
                "SELECT * FROM users WHERE userName = ?"
            );
            $stmt->bind_param("s", $uname);
            $stmt->execute();
            $result = $stmt->get_result();
            if($result->num_rows > 1){
                $this->params['debugMessage'] =
                    "Login error: more than one user with the given username!";
            }else if($result->num_rows > 0){
                $row = $result->fetch_assoc();
                if(!password_verify($pword, $row['pHash'])){
                    $row=false;
                }
            }
            $stmt->close();
            if($row === false){
                $this->params['errorMessage'] = "Wrong username or password.";
                $stmt = $this->db->prepareQuery(
                /** @lang MySQL */
                    "INSERT INTO failureLogs ".
                    "(userName, accessTime, IP, isIPv6) ".
                    "VALUES (?, NOW(), UNHEX(?), ?);"
                );
                $addr = $this->request->server->get('REMOTE_ADDR');
                $rawAddr = bin2hex(inet_pton($addr));
                $isIPv6 = filter_var(
                        $addr,
                        FILTER_VALIDATE_IP,
                        FILTER_FLAG_IPV6
                    )!==false;
                //var_dump($isIPv6);
                $stmt->bind_param(
                    "ssi",
                    $uname,
                    $rawAddr,
                    $isIPv6
                );
                $stmt->execute();
            }
        } else {
            $this->params['errorMessage'] = "You are rate limited. Chillax, bruh.";
        }
        if(is_array($row)) {
            $securityInfo = [];
            $securityInfo['userID'] = (int)$row['ID'];
            $securityInfo['accessLevel'] = (int)$row['accessLevel'];
            $securityInfo['userName'] = $row['userName'];
            $securityInfo['startTime'] = ($clock)();
            $securityInfo['userIP'] = $this->request->server->get('REMOTE_ADDR');
            try {
                $parser = Parser::create();
                $result = $parser->parse(
                    $this->request->server->get('HTTP_USER_AGENT')
                );

                $securityInfo['OS'] = $result->os->toString();
                $securityInfo['browser'] = $result->ua->family;
                //get geolocation data from freegeoip (and drop line break)
                $securityInfo['geoIP'] =
                    substr(file_get_contents(
                        "https://freegeoip.net/csv/" .
                        $securityInfo['userIP']
                    ), 0, -2);
                $sections = explode(",", $securityInfo['geoIP']);
                if ($sections[2] != "") {
                    $securityInfo['countryName'] = $sections[2];
                }
                if ($sections[5] != "") {
                    $securityInfo['city'] = $sections[5];
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
            }
            return new SecurityContext($clock, $lifetime, $securityInfo);
        }else{
            return new SecurityContext($clock, $lifetime);
        }
    }

    public function GetParams(){
        return $this->params;
    }
}