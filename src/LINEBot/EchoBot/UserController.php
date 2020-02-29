<?php


namespace LINE\LINEBot\EchoBot;


use LINE\LINEBot\Constant\HTTPHeader;
use LINE\LINEBot\Event\MessageEvent;
use LINE\LINEBot\Event\MessageEvent\TextMessage;
use LINE\LINEBot\Exception\InvalidEventRequestException;
use LINE\LINEBot\Exception\InvalidSignatureException;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class UserController
{
    private $view;
    private $logger;
    protected $table;

    public function index (Request $req, Response $res)
    {
        $servername = "us-cdbr-iron-east-04.cleardb.net";
        $username = "b1f3fa9bda05bb";
        $password = "10d0741f";
        $dbname = "heroku_fdb27654ad74a1b";

        $conn = mysqli_connect($servername, $username, $password, $dbname);

        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        echo "Connected successfully";

        $sql = "SELECT * FROM help";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo $row["id"]. " " . $row["name"] . "<br>";
            }
        } else {
            echo "0 results";
        }

        $conn->close();
    }

    public function help(Request $req, Response $res){
        /** @var \LINE\LINEBot $bot */
            $bot = $this->bot;
            /** @var \Monolog\Logger $logger */
            $logger = $this->logger;

            $signature = $req->getHeader(HTTPHeader::LINE_SIGNATURE);
            if (empty($signature)) {
                return $res->withStatus(400, 'Bad Request');
            }

            // Check request with signature and parse request
            try {
                $events = $bot->parseEventRequest($req->getBody(), $signature[0]);
            } catch (InvalidSignatureException $e) {
                return $res->withStatus(400, 'Invalid signature');
            } catch (InvalidEventRequestException $e) {
                return $res->withStatus(400, "Invalid event request");
            }

            $str = "1";

            foreach ($events as $event) {
                if ($event->getText() == 'help') {
                    $servername = "us-cdbr-iron-east-04.cleardb.net";
                    $username = "b1f3fa9bda05bb";
                    $password = "10d0741f";
                    $dbname = "heroku_fdb27654ad74a1b";

                    $conn = mysqli_connect($servername, $username, $password, $dbname);

                    if (!$conn) {
                        die("Connection failed: " . mysqli_connect_error());
                    }
                    echo "Connected successfully";

                    $sql = "SELECT * FROM help";
                    $result = $conn->query($sql);


                    if ($result->num_rows > 0) {
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            $str .= ($row["id"]. " " . $row["name"] . "<br>");
                        }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                }
                $replyText = $str;
                $logger->info('Reply text: ' . $replyText);
                $resp = $bot->replyText($event->getReplyToken(), $replyText);
                $logger->info($resp->getHTTPStatus() . ': ' . $resp->getRawBody());
            }

            $res->write('OK');
            return $res;
    }
}