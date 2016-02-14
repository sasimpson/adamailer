<?php
namespace Mailer;
require_once(__DIR__ . "/../src/Mailer.php");

class MailerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->apikey = "foobarbaz-test";
        $this->domain = "foobar.com";
        $this->to = "foo@bar.com";
        $this->from = "bar@foo.com";
        $this->subject = "Mailform Test";
        $_POST = array();
        // $this->_mgMock = $this->getMock("Mailgun", array("sendMessage"));
        // $this->_mgMock = $this->getMockBuilder("Mailgun\Mailgun")
        //              ->setConstructorArgs(array($this->domain))
        //              ->disableOriginalConstructor()
        //              ->getMock();
        // $this->_mgMock = $this->prophesize("Mailgun");
    }
    public function tearDown() {
        unset($_POST);
    }

    public function testAddField() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $count = $mailer->getFields();
        $this->assertCount(0, $count);
        $mailer->addField("Foo", "foo", "string");
        $count = $mailer->getFields();
        $this->assertCount(1, $count);
        $mailer->addField("Bar", "bar", "int");
        $count = $mailer->getFields();
        $this->assertCount(2, $count);
    }

    public function testMessageGeneration() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->setMessage("This is a test message");
        $message = $mailer->getMessage();
        $this->assertEquals("This is a test message\n", $message);
    }

    public function testMessageGenerationWithFields() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->setMessage("This is a test message");
        $mailer->addField("Foo", "foo", "string");
        $mailer->addField("Bar", "bar", "number");
        $_POST["foo"] = "one";
        $_POST["bar"] = 2;
        $message = $mailer->getMessage();
        $this->assertEquals("This is a test message\nFoo:\tone\nBar:\t2\n", $message);
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Foo cannot be empty
     */
    public function testEmptyFieldValidation() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "string", True);
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Foo is not valid (should be a string)
     */
    public function testWrongTypeFieldValidationString() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "string", TRUE);
        $_POST["foo"] = "";
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    /**
     * @expectedException Exception
     * @expectedExceptionMessage Foo is not valid (should be a number)
     */
    public function testWrongTypeFieldValidationNumber() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "number", TRUE);
        $_POST["foo"] = "Test String";
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Foo is not a valid email address
    */
    public function testWrongTypeFieldValidationEmail() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "email", TRUE);
        $_POST["foo"] = 'thing.com';
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    /**
    * @expectedException Exception
    * @expectedExceptionMessage Foo cannot be empty
    */
    public function testEmptyValidation() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "string", TRUE);
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    public function testNoValidation() {
        $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
        $mailer->addField("Foo", "foo", "string", False);
        $_POST["foo"] = 2000;
        $mailer->gatherFields();
        $mailer->validateFields();
    }

    // public function testSendMessage() {
    //     $this->_mgMock->expects($this->once())->method("sendMessage")->willReturn($this->returnValue(null));
    //
    //     $_POST["foo"] = "MyName";
    //     $mailer = new Mailer($this->apikey, $this->domain, $this->to, $this->from, $this->subject);
    //     $mailer->addField("Foo", "foo", "string", True);
    //     $mailer->setMessage("Data:");
    //     $mesage = $mailer->getMessage();
    //     $mailer->send();
    //     $this->assertEquals(null, $mock->test());
    // }
}

?>
