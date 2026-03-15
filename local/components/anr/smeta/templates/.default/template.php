<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
global $USER;

$kCol = $arResult["KCOL"];
$countCat = $arResult["COUNT_CAT"];

$APPLICATION->AddHeadScript($templateFolder.'/script.js');
?>

<h2>Estimate for application <?=htmlspecialcharsbx($arResult["ZAYAVKA_NAME"])?></h2>

<?if(($_GET["saved"] ?? "") === "Y"):?>
	<div class="news-yes">Estimate saved successfully</div>
<?endif;?>

<div class="b-index-form">
  <form method="post" enctype="multipart/form-data" id="smetaform">

  <?=bitrix_sessid_post()?>

  <input type="hidden" name="zayvka" value="<?=$arResult["ZAYAVKA_ID"]?>">
  <input type="hidden" name="kCol" value="<?=$kCol?>">

  <div class="b-form">

	<?for ($i=0; $i < $countCat; $i++):?>

	<h5><?=$arResult["CAT_NAMES"][$i]?></h5>

	  <div class="b-form-section">
		<table class="b-applications-smeta" data-smeta-cat="<?=$i?>" data-col="<?=$kCol?>">
		  <tbody class="smeta-body">
			<tr>
			  <td class="application_th">Item name</td>
			  <td class="application_th">Unit price (RUB)</td>
			  <td class="application_th">Quantity</td>
			  <td class="application_th">Total cost (RUB)</td>
			</tr>

			<?// estimate rows
			$rows = $arResult["SMETA"][$i]["rows"] ?: $arResult["JCAT"][$i];?>
			
			<?foreach ($rows as $j => $row):?>
			<?$name = is_array($row) ? $row["name"] : $row;
			  $cols = $row["cols"] ?? [];?>

			<tr class="smeta-row">
			  <td class="application_th">
				<input type="text" name="cat[<?=$i?>][<?=$j?>]" id="cat<?=$i?>_<?=$j?>" value="<?=$name?>">
			  </td>
			  <?for ($k=1; $k<$kCol; $k++):?>
				<td>
				  <input type="number" step="1" name="pay[<?=$i?>][<?=$j?>][<?=$k?>]" id="pay<?=$i?>_<?=$j?>_<?=$k?>" value="<?=$cols[$k] ?? ""?>">
				</td>
			  <?endfor;?>
			</tr>
			<?endforeach;?>

			<tr>
			  <td colspan="<?=$kCol?>">
				<button type="button" class="form-add-button" data-smeta-cat="<?=$i?>">
				+ Add row
				</button>
				<button type="button" class="form-del-button" data-smeta-cat="<?=$i?>">
				- Remove row
				</button>
			 </td>
			</tr>

			<tr>
			  <td colspan="<?=$kCol?>">
				Comment: <textarea name="comment[<?=$i?>]"><?=$arResult["SMETA"][$i]["comment"] ?? ""?></textarea>
			  </td>
			</tr>

			<tr>
			  <td class="application_th">
				Total for category <?=($i+1)?>
			  </td>
			  <?for ($k=1; $k<$kCol-1; $k++):?>
				<td></td>
			  <?endfor;?>
			  <td>
				<input type="number" step="1" name="itog[<?=$i?>]" id="itog<?=$i?>" value="<?=$arResult["ITOG"][$i] ?? ""?>">
			  </td>
			</tr>
		  </tbody>
		</table>
	  <?endfor;?>
	  
	  <div class="b-form-sog">
		<input name="dannie" type="checkbox" value="75" required>
		<div class="mf-text-2"><span style="color:red;">*</span>
		  I agree to the processing of my personal data and accept the 
		  <a href="/about/privacy-policy/">Privacy Policy</a>
			<input name="bitrixid" type="hidden" value="<?=$USER->GetID();?>">
		</div>
	  </div>
	  <div class="b-form-section-buttons">	
		<input type="submit" name="submit" value="Submit">
	  </div>
</form>
