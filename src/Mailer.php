<?php
    namespace Mailer;

    require 'vendor/autoload.php';
    use Mailgun\Mailgun;

    /* php mailer */
    class Mailer {
        private $to_emails;
        private $from_email;
        private $subject;
        private $message;
        private $apikey;
        private $domain;
        private $fields = array();

        public function __construct($to, $from, $subject) {
            $this->to_emails = $to;
            $this->from_email = $from;
            $this->subject = $subject;
        }

        public function send(){
            $mg = new Mailgun($this->apikey);
            $mg->sendMessage($this->domain, array(
                                            'from'    => $this->from_email,
                                            'to'      => $this->to_emails,
                                            'subject' => $this->subject,
                                            'text'    => $this->message));
        }

        public function setMessage($message) {
            $this->message = $message;
        }

        public function getMessage(){
            $this->gatherFields();
            $this->validateFields();
            $message = $this->message . "\n";
            $updated_fields = array();
            foreach ($this->fields as $item) {
                $message .= $item["name"] . ":\t" . $item["value"] . "\n";
            }
            return $message;
        }

        public function gatherFields(){
            $updated_fields = array();
            foreach ($this->fields as $item) {
                if (array_key_exists("foo", $_POST)) {
                    $item["value"] = $_POST[$item["field"]];
                }
                // else {
                //     $item["value"] = "";
                // }
                array_push($updated_fields, $item);
            }
            $this->fields = $updated_fields;
        }

        public function addField($name, $field_name, $type, $validate = FALSE) {
            array_push(
                $this->fields,
                [
                    "name" => $name,
                    "field" => $field_name,
                    "type" => $type,
                    "validate" => $validate
                ]
            );
        }

        public function getFields(){
            return $this->fields;
        }

        public function validateFields(){
            if (sizeof($this->fields) != 0) {
                foreach ($this->fields as $item) {
                    if ($item["validate"]){
                        if (array_key_exists("value", $item)){
                            switch ($item["type"]) {
                                case "string":
                                    if (!is_string($item["value"]) || $item["value"] == "")
                                        throw new \Exception($item["name"] . " is not valid (should be a string)");
                                    break;
                                case "number":
                                    if (!is_numeric($item["value"]))
                                        throw new \Exception($item["name"] . " is not valid (should be a number)");
                                    break;
                                case "email":
                                    if (!filter_var($item["value"], FILTER_VALIDATE_EMAIL))
                                        throw new \Exception($item["name"] . " is not a valid email address");
                                    break;
                            }

                        } else {
                            throw new \Exception($item["name"] . " cannot be empty");
                        }
                    }
                }
            } else {
                return;
            }
        }
    }

?>
