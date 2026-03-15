<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

CModule::IncludeModule("iblock");
global $USER;
$arResult = [];
//$arResult: EDIT_MODE, ZAYAVKA_ID, ZAYAVKA_NAME, SMETA, JCAT, CAT_NAMES, KCOL

$ZAYAVKA_ID = (int)($_REQUEST["ZAYAVKA"] ?? 0);

if (!$USER->IsAuthorized() || !$ZAYAVKA_ID)
{
    ShowError("Page not found");
    return;
}

$arResult["ZAYAVKA_ID"] = $ZAYAVKA_ID;
$arResult["USER_ID"] = $USER->GetID();

$iblockZayavka = (int)$arParams["IBLOCK_ZAYAVKA"];
$iblockSmeta   = (int)$arParams["IBLOCK_SMETA"];
$arResult["JCAT"] = $arParams["PRESET_CAT"];

//names of the categories are the names of Bitrix infoblock's properties
$arResult["CAT_NAMES"] = [];
$arResult["ITOG"] = [];

$payProps = [];
$propRes = CIBlockProperty::GetList(["SORT"=>"ASC"],["IBLOCK_ID"=>$iblockSmeta, "ACTIVE"=>"Y"]);
while ($prop = $propRes->Fetch())
{
    if (preg_match('/PAY_(\d+)/', $prop["CODE"], $m))
    {
        $index = (int)$m[1] - 1;
        $payProps[$index] = [
            "CODE"=>$prop["CODE"],
            "NAME"=>$prop["NAME"]
        ];
    }
}
ksort($payProps);
foreach($payProps as $index => $prop){
		$arResult["CAT_NAMES"][$index] = $prop["NAME"];
}
$arResult["COUNT_CAT"] = count($payProps);
for ($i=0; $i<$arResult["COUNT_CAT"]; $i++)
{
    if (!isset($arResult["JCAT"][$i]))
        $arResult["JCAT"][$i] = [""];
}

$arResult["KCOL"] = 4;

$comissionGroup = (int)$arParams["COMMISSION_GROUP"];
$arGroups = CUser::GetUserGroup($USER->GetID());
$isComission = in_array($comissionGroup, $arGroups);

$arFilter = ["IBLOCK_ID" => $iblockZayavka, "ID" => $ZAYAVKA_ID, "ACTIVE" => "Y"];
$arSelect = ["ID", "NAME", "CREATED_BY", "PROPERTY_BOSS"];
$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
if ($ar_res = $res->GetNext())
{
  if ($ar_res["CREATED_BY"] != $USER->GetID() && !$USER->isAdmin() && !$isComission)
    {ShowError("Access denied");
        return;
    }
    $arResult["ZAYAVKA_NAME"] = $ar_res["NAME"];
}
else
{
    ShowError("Application is not found");
    return;
}
// ======================================
// look for the existing estimate
// ======================================

$arFilter = ["IBLOCK_ID" => $iblockSmeta, "ACTIVE" => "Y", "PROPERTY_ZAYVKA" => $ZAYAVKA_ID];
$arSelect = ["ID", "NAME", "PROPERTY_SMETA_JSON"];
foreach ($payProps as $prop)
{
    $arSelect[] = "PROPERTY_".$prop["CODE"];
}
$res = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

if ($arSmeta = $res->GetNext())
{
    $arResult["EDIT_MODE"] = true;
    $arResult["SMETA_ID"] = $arSmeta["ID"];
	  foreach ($payProps as $index => $prop)
      {
        $code = $prop["CODE"];
        $arResult["ITOG"][$index] = $arSmeta["PROPERTY_".$code."_VALUE"];
    }

    $json = $arSmeta["PROPERTY_SMETA_JSON_VALUE"]["TEXT"] ?? "";
    if ($json)
    {
        $json = html_entity_decode($json);
        $arResult["SMETA"] = json_decode($json, true) ?: [];
    }
}
else
{
    $arResult["EDIT_MODE"] = false;
    $arResult["SMETA"] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && check_bitrix_sessid())
{
    include __DIR__."/save.php";
}

CJSCore::Init(["jquery", "date"]);
$this->IncludeComponentTemplate();
