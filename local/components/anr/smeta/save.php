<?
if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($_POST["zayvka"]))
{
	global $USER, $APPLICATION;
	CModule::IncludeModule('iblock'); 
    $el = new CIBlockElement;
    $iblock_id = (int)$arParams["IBLOCK_SMETA"];
	$zayavka_id = (int)$_POST['zayvka'];
    $PROP = array();
   	$PROP['ZAYVKA'] = $zayavka_id;	
	$PROP['DANNIE'] = $_POST['dannie'];
	$smeta = [];
	$kCol = (int)($_POST["kCol"] ?? 4);
	if (!empty($_POST["cat"])){
	  foreach ($_POST["cat"] as $i => $rows)
		{
		  $smeta[$i] = [
          "rows" => [],
          "comment" => $_POST["comment"][$i] ?? "",
		  ];
		foreach ($rows as $j => $name)
		{
		  if ($name === "")
			continue;
          $row = [
           "name" => trim($name),
           "cols" => []
          ];
		  for ($k = 1; $k < $kCol; $k++)
			  {
				$value = $_POST["pay"][$i][$j][$k] ?? 0;
				$value = str_replace([' ', ','], ['', '.'], $value);
				$row["cols"][$k] = (float)$value;
			  }
		  $smeta[$i]["rows"][] = $row;
		}
	  }
    }

	$json = json_encode($smeta, JSON_UNESCAPED_UNICODE) ?: "{}";

	$PROP["SMETA_JSON"] = ["VALUE" => [
		"TEXT" => $json,
        "TYPE" => "text"
      ]];
	if (!empty($_POST["itog"])){
	  foreach ($_POST["itog"] as $i => $itog)
		{
		  $PROP["PAY_".($i+1)] = $itog;
		}
	}
// ===== ADD / UPDATE =====

if ($arResult["EDIT_MODE"])
{
    CIBlockElement::SetPropertyValuesEx($arResult["SMETA_ID"], $iblock_id, $PROP);
}
else
{
    $fields = [
        "CREATED_BY" => $USER->GetID(),
        "IBLOCK_ID" => $iblock_id,
        "PROPERTY_VALUES" => $PROP,
        "NAME" =>  "Estimate for the application ".$arResult["ZAYAVKA_NAME"].". Date ".date("d.m.Y H:i:s"),
        "ACTIVE" => "Y"
    ];
    $el->Add($fields);
}

LocalRedirect($APPLICATION->GetCurPage()."?ZAYAVKA=".$zayavka_id."&saved=Y");
exit;
}
?>
