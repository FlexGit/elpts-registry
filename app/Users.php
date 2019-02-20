<?php

namespace App;

use PhpOffice\PhpSpreadsheet\Shared\XMLWriter;
use SoapClient;
use SoapVar;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class Users extends Model
{
    protected $table = 'elpts_users';
    protected $fillable = ['name', 'snils', 'ogrn', 'admin', 'enable'];

    /**
     * Get Template User Roles.
     *
     * @param  int  $templates_id
     * @param  int  $user_id
     * @return array $values_arr
     */
    public function getTemplateUserRoles($templates_id, $user_id)
    {
		$values = DB::table('elpts_templates_users_roles')
			->select('elpts_templates_users_roles.templates_id', 'elpts_templates_users_roles.users_id', 'elpts_templates_users_roles.roles_id')
			->where([
				['templates_id', '=', $templates_id],
				['users_id', '=', $user_id],
				['enable', '=', DB::raw(1)],
			])
			->get();

		$values_arr = [];
		if (count($values) > 0)
		{
        	foreach ($values->all() as $value)
        	{
        		$values_arr[$value->users_id][$value->templates_id][$value->roles_id] = 1;
        	}
        }

        return $values_arr;
    }

    /**
     * Verify Signature by Signal-COM DSS Server
     *
     * @param  string  $file
     * @param  string  $signature
     * @return array
     */
    public function signatureVerify($file, $signature)
    {
		$xw = new XMLWriter();
		$xw->openMemory();
		$xw->setIndent(true);
		$xw->startElementNS('ns1', 'verifyRequest', NULL);
			$xw->startAttribute('xmlns');
				$xw->text('urn:oasis:names:tc:dss:1.0:core:schema');
			$xw->endAttribute();
			$xw->writeAttributeNS('xmlns', 'ns6', NULL, 'http://uri.etsi.org/02231/v2#');
			$xw->writeAttributeNS('xmlns', 'ns5', NULL, 'urn:oasis:names:tc:dss-x:1.0:profiles:verificationreport:schema#');
			$xw->writeAttributeNS('xmlns', 'ns7', NULL, 'http://signalcom.ru/2018/01/oasis/dss/extension');
			$xw->writeAttributeNS('xmlns', 'ns2', NULL, 'http://www.w3.org/2000/09/xmldsig#');
			$xw->writeAttributeNS('xmlns', 'ns4', NULL, 'urn:oasis:names:tc:dss:1.0:profiles:AdES:schema#');
			$xw->writeAttributeNS('xmlns', 'ns3', NULL, 'http://uri.etsi.org/01903/v1.3.2#');
			$xw->writeAttributeNS('xmlns', 'dss', NULL, 'http://dss.oasis.signalcom.ru/');
			$xw->writeAttributeNS('xmlns', 'urn', NULL, 'urn:oasis:names:tc:dss:1.0:core:schema');
			$xw->writeAttributeNS('xmlns', 'xd', NULL, 'http://www.w3.org/2000/09/xmldsig#');
			$xw->startElementNS('urn', 'InputDocuments', NULL);
				$xw->startElement("Document");
					$xw->startElement("Base64Data");
						$xw->text(base64_encode($file));
					$xw->endElement();
				$xw->endElement();
			$xw->endElement();
			$xw->startElementNS('urn', 'SignatureObject', NULL);
				$xw->startElement("Base64Signature");
					$xw->startAttribute('Type');
						$xw->text('urn:ietf:rfc:3369');
					$xw->endAttribute();
					$xw->text($signature);
				$xw->endElement();
			$xw->endElement();
		$xw->endElement();

		try
		{
			$client = new SoapClient(config('constants.dss_uri'), [
		        "soap_version" => SOAP_1_1,
		        "trace" => 1,
		        "style" => SOAP_DOCUMENT,
		        "use" => SOAP_LITERAL,
		        "cache_wsdl" => WSDL_CACHE_NONE
		 	]);

		    $verifyResponse = json_decode(json_encode($client->__soapCall('verify', [new SoapVar($xw->outputMemory(), XSD_ANYXML)])), true);

		    $xw = null;

		    if (($verifyResponse["Result"]["ResultMajor"] != 'urn:oasis:names:tc:dss:1.0:resultmajor:Success') || ($verifyResponse["Result"]["ResultMajor"] == 'urn:oasis:names:tc:dss:1.0:resultmajor:Success' && $verifyResponse["Result"]["ResultMinor"] == 'urn:oasis:names:tc:dss:1.0:resultminor:invalid:IncorrectSignature'))
		    {
				return [
					'error' => [0 => $verifyResponse["Result"]["ResultMinor"]],
				];
		    }
		}
		catch (SoapFault $fault)
		{
			return [
				'error' => [0 => $fault->getMessage()],
			];
		}

		return [
			'error' => [],
		];
    }
}
