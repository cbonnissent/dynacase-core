<?php
/*
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package FDL
*/

namespace Dcp\Pu;
/**
 * @author Anakeen
 * @license http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU Affero General Public License
 * @package Dcp\Pu
 */

require_once 'PU_testcase_dcp_commonfamily.php';

class TestAttributeDefault extends TestCaseDcpCommonFamily
{
    /**
     * import TST_DEFAULTFAMILY1 family
     * @static
     * @return string
     */
    protected static function getCommonImportFile()
    {
        return "PU_data_dcp_familydefault.ods";
    }
    
    protected $famName = 'TST_DEFAULTFAMILY1';
    /**
     * @dataProvider dataDefaultValues
     */
    public function testDefaultValue($famid, $attrid, $expectedvalue)
    {
        
        $d = createDoc(self::$dbaccess, $famid);
        $this->assertTrue(is_object($d) , sprintf("cannot create %s document", $famid));
        
        $oa = $d->getAttribute($attrid);
        $this->assertNotEmpty($oa, sprintf("attribute %s not found in %s family", $attrid, $famid));
        $value = $d->getValue($oa->id);
        $this->assertEquals($expectedvalue, $value, sprintf("not the expected default value attribute %s", $attrid));
    }
    /**
     * @dataProvider dataDefaultParamValues
     */
    public function testDefaultParamValue($famid, $attrid, $expectedvalue)
    {
        
        $d = createDoc(self::$dbaccess, $famid, false, false);
        $this->assertTrue(is_object($d) , sprintf("cannot create %s1 document", $famid));
        
        $oa = $d->getAttribute($attrid);
        $this->assertNotEmpty($oa, sprintf("attribute %s not found in %s family", $attrid, $famid));
        $value = $d->getParamValue($oa->id);
        $this->assertEquals($expectedvalue, $value, sprintf("not the expected default value attribute %s", $attrid));
    }
    /**
     * @dataProvider dataDefaultInheritedValues
     */
    public function testDefaultInheritedValue($famid, array $expectedvalues, array $expectedParams)
    {
        $d = createDoc(self::$dbaccess, $famid);
        $this->assertTrue(is_object($d) , sprintf("cannot create %s document", $famid));
        
        foreach ($expectedvalues as $attrid => $expectedValue) {
            $oa = $d->getAttribute($attrid);
            $this->assertNotEmpty($oa, sprintf("attribute %s not found in %s family", $attrid, $famid));
            $value = $d->getValue($oa->id);
            
            $this->assertEquals($expectedValue, $value, sprintf("not the expected default value attribute %s", $attrid));
        }
        foreach ($expectedParams as $attrid => $expectedValue) {
            $oa = $d->getAttribute($attrid);
            $this->assertNotEmpty($oa, sprintf("parameter %s not found in %s family", $attrid, $famid));
            $value = $d->getParamValue($oa->id);
            $this->assertEquals($expectedValue, $value, sprintf("not the expected default value parameter %s", $attrid));
        }
    }
    /**
     * @dataProvider dataDefaultInherited
     */
    public function testDefaultInherited($famid, array $expectedvalues, array $expectedParams)
    {
        /**
         * @var  \DocFam $d
         */
        $d = new_Doc(self::$dbaccess, $famid);
        $this->assertTrue(is_object($d) , sprintf("cannot get %s family", $famid));
        
        foreach ($expectedvalues as $attrid => $expectedValue) {
            
            $value = $d->getDefValue($attrid);
            $this->assertEquals($expectedValue, $value, sprintf("not the expected default value attribute %s", $attrid));
        }
        foreach ($expectedParams as $attrid => $expectedValue) {
            
            $value = $d->getParamValue($attrid);
            $this->assertEquals($expectedValue, $value, sprintf("not the expected default value parameter %s", $attrid));
        }
    }
    /**
     * @dataProvider dataWrongValue
     */
    public function testWrongValue($famid, $errorCode)
    {
        $err = '';
        try {
            $d = createDoc(self::$dbaccess, $famid);
            $this->assertNotEmpty($err, sprintf(" no error returned, must have %s", $errorCode));
        }
        catch(\Dcp\Exception $e) {
            $err = $e->getDcpCode();
            $this->assertEquals($errorCode, $err, sprintf("not the good error code : %s", $e->getMessage()));
        }
    }
    /**
     * @dataProvider dataInitialParam
     */
    public function testInitialParam($famid, $attrid, $expectedValue, $expectedDefaultValue)
    {
        $d = createDoc(self::$dbaccess, $famid);
        $value = $d->getParamValue($attrid);
        $this->assertEquals($expectedValue, $value, sprintf("parameter %s has not correct initial value", $attrid));
        $f = $d->getFamDoc();
        $f->setParam($attrid, '');
        $f->modify();
        $d2 = createDoc(self::$dbaccess, $famid);
        $f = $d2->getFamDoc();
        $value = $d2->getParamValue($attrid);
        $this->assertEquals($expectedDefaultValue, $value, sprintf("parameter %s has not correct default value", $attrid));
    }
    
    public function dataInitialParam()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY2",
                "TST_P4",
                40,
                ''
            ) ,
            array(
                "TST_DEFAULTFAMILY2",
                "TST_P5",
                50,
                34
            )
        );
    }
    public function dataWrongValue()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY7",
                "DFLT0009"
            ) ,
            array(
                "TST_DEFAULTFAMILY8",
                "DFLT0009"
            ) ,
            array(
                "TST_DEFAULTFAMILY9",
                "DFLT0008"
            )
        );
    }
    public function dataDefaultInherited()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY2",
                array(
                    "TST_TITLE" => "First",
                    "TST_NUMBER1" => "::isOne()",
                    "TST_NUMBER2" => "::oneMore(TST_NUMBER1)",
                    "TST_NUMBER3" => "::oneMore(2)"
                ) ,
                array(
                    'TST_P1' => 'PFirst',
                    "TST_P2" => "10",
                    "TST_P3" => "::oneMore(TST_P2)"
                )
            ) ,
            array(
                "TST_DEFAULTFAMILY3",
                array(
                    "TST_TITLE" => "Second",
                    "TST_NUMBER1" => "::isOne()",
                    "TST_NUMBER2" => "::simpleAdd(12,TST_NUMBER1)",
                    "TST_NUMBER3" => "::oneMore(2)"
                ) ,
                array(
                    'TST_P1' => 'PSecond',
                    "TST_P2" => "10",
                    "TST_P3" => "::oneMore(TST_P2)"
                )
            ) ,
            array(
                "TST_DEFAULTFAMILY4",
                array(
                    "TST_TITLE" => "Third",
                    "TST_NUMBER1" => "::isOne()",
                    "TST_NUMBER2" => "::oneMore(TST_NUMBER1)",
                    "TST_NUMBER3" => ""
                ) ,
                array(
                    'TST_P1' => 'PThird',
                    "TST_P2" => "10",
                    "TST_P3" => "::oneMore(TST_P2)"
                )
            )
        );
    }
    
    public function dataDefaultInheritedValues()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY2",
                array(
                    "TST_TITLE" => "First",
                    "TST_NUMBER1" => "1",
                    "TST_NUMBER2" => "2",
                    "TST_NUMBER3" => "3"
                ) ,
                array(
                    'TST_P1' => 'PFirst',
                    "TST_P2" => "10",
                    "TST_P3" => "11"
                )
            ) ,
            array(
                "TST_DEFAULTFAMILY3",
                array(
                    "TST_TITLE" => 'Second',
                    "TST_NUMBER1" => "1",
                    "TST_NUMBER2" => "13",
                    "TST_NUMBER3" => "3"
                ) ,
                array(
                    "TST_P1" => 'PSecond',
                    "TST_P2" => "10",
                    "TST_P3" => "11"
                )
            ) ,
            array(
                "TST_DEFAULTFAMILY4",
                array(
                    "TST_TITLE" => 'Third',
                    "TST_NUMBER1" => "1",
                    "TST_NUMBER2" => "2",
                    "TST_NUMBER3" => ""
                ) ,
                array(
                    "TST_P1" => 'PThird',
                    "TST_P2" => "10",
                    "TST_P3" => "11"
                )
            )
        );
    }
    
    public function dataDefaultValues()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TITLE',
                'The title'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER1',
                '1'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER2',
                '2'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER3',
                '3'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER4',
                '4'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER5',
                '5'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER6',
                '50'
            ) ,
            
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER7',
                '53'
            ) ,
            
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER8',
                '6'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_NUMBER9',
                '11'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT1',
                'TST_TITLE'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT2',
                'TST_TITLE'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT3',
                'TST_TITLE,TST_TITLE'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT4',
                'it is,simple word,testing'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT5',
                'it\'s,a "citation",and "second"'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT6',
                '[:TST_TITLE:]'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_TEXT7',
                "0"
            ) ,
            array(
                "TST_DEFAULTFAMILY5",
                'TST_TITLE',
                "First"
            ) ,
            array(
                "TST_DEFAULTFAMILY5",
                'TST_NUMBER1',
                "1"
            ) ,
            array(
                "TST_DEFAULTFAMILY5",
                'TST_TEXT1',
                "cellule un"
            ) ,
            array(
                "TST_DEFAULTFAMILY5",
                'TST_NUMBER2',
                ""
            ) ,
            array(
                "TST_DEFAULTFAMILY5",
                'TST_TEXT2',
                ""
            ) ,
            array(
                "TST_DEFAULTFAMILY6",
                'TST_TEXT1',
                "Un\nDeux"
            ) ,
            array(
                "TST_DEFAULTFAMILY6",
                'TST_TEXT2',
                "First\nSecond"
            ) ,
            array(
                "TST_DEFAULTFAMILY6",
                'TST_NUMBER2',
                "10\n20"
            ) ,
            array(
                "TST_DEFAULTFAMILY6",
                'TST_DOCM2',
                "9<BR>11\n12<BR>13"
            )
        );
    }
    
    public function dataDefaultParamValues()
    {
        return array(
            array(
                "TST_DEFAULTFAMILY1",
                'TST_P1',
                'test one'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_P2',
                '10'
            ) ,
            array(
                "TST_DEFAULTFAMILY1",
                'TST_P3',
                '11'
            )
        );
    }
}
?>