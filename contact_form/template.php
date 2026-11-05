<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

$questions = isset($arResult["QUESTIONS"]) && is_array($arResult["QUESTIONS"])
    ? $arResult["QUESTIONS"]
    : array();

$hiddenFields = array();
$inputFields = array();
$messageFields = array();

foreach ($questions as $fieldSid => $question) {
    $htmlCode = isset($question["HTML_CODE"]) ? (string)$question["HTML_CODE"] : "";
    $fieldType = "";

    if (isset($question["STRUCTURE"][0]["FIELD_TYPE"])) {
        $fieldType = strtolower((string)$question["STRUCTURE"][0]["FIELD_TYPE"]);
    }

    if ($fieldType === "hidden") {
        $hiddenFields[$fieldSid] = $question;
        continue;
    }

    if ($fieldType === "textarea" || stripos($htmlCode, "<textarea") !== false) {
        $messageFields[$fieldSid] = $question;
    } else {
        $inputFields[$fieldSid] = $question;
    }
}

$renderQuestion = static function ($fieldSid, $question) use ($arResult) {
    $caption = isset($question["CAPTION"]) ? trim(strip_tags((string)$question["CAPTION"])) : "";
    $required = isset($question["REQUIRED"]) && $question["REQUIRED"] === "Y";
    $htmlCode = isset($question["HTML_CODE"]) ? (string)$question["HTML_CODE"] : "";

    if ($required && ($caption === "" || substr($caption, -1) !== "*")) {
        $caption .= "*";
    }

    $errorText = "";
    if (isset($arResult["FORM_ERRORS"]) && is_array($arResult["FORM_ERRORS"]) && isset($arResult["FORM_ERRORS"][$fieldSid])) {
        $fieldError = $arResult["FORM_ERRORS"][$fieldSid];
        $errorText = is_array($fieldError) ? implode(" ", $fieldError) : (string)$fieldError;
    }

    ?>
    <div class="input contact-form__input<?php echo $errorText !== "" ? " is-error" : ""; ?>">
        <label class="input__label">
            <span class="input__label-text"><?php echo htmlspecialcharsbx($caption); ?></span>
            <?php echo $htmlCode; // Bitrix-generated input markup; keep its field name and validation attributes. ?>
            <?php if ($errorText !== ""): ?>
                <span class="input__notification"><?php echo htmlspecialcharsbx($errorText); ?></span>
            <?php endif; ?>
        </label>
    </div>
    <?php
};

$isSuccess = isset($arResult["isFormNote"]) && $arResult["isFormNote"] === "Y";
?>

<section class="contact-form" aria-labelledby="contact-form-title">
    <div class="contact-form__head">
        <h2 class="contact-form__head-title" id="contact-form-title">Связаться</h2>
        <p class="contact-form__head-text">
            Наши сотрудники помогут выполнить подбор услуги и расчет цены с учетом ваших требований.
        </p>
    </div>

    <?php if ($isSuccess): ?>
        <div class="contact-form__success" role="status">
            <?php echo htmlspecialcharsbx(strip_tags((string)$arResult["FORM_NOTE"])); ?>
        </div>
    <?php else: ?>
        <?php echo isset($arResult["FORM_HEADER"]) ? $arResult["FORM_HEADER"] : ""; ?>

        <?php if (isset($arResult["isFormErrors"]) && $arResult["isFormErrors"] === "Y" && !empty($arResult["FORM_ERRORS_TEXT"])): ?>
            <div class="contact-form__errors" role="alert">
                <?php echo htmlspecialcharsbx(strip_tags((string)$arResult["FORM_ERRORS_TEXT"])); ?>
            </div>
        <?php endif; ?>

        <?php foreach ($hiddenFields as $hiddenQuestion): ?>
            <?php echo isset($hiddenQuestion["HTML_CODE"]) ? $hiddenQuestion["HTML_CODE"] : ""; ?>
        <?php endforeach; ?>

        <div class="contact-form__form">
            <div class="contact-form__form-inputs">
                <?php foreach ($inputFields as $fieldSid => $question): ?>
                    <?php $renderQuestion($fieldSid, $question); ?>
                <?php endforeach; ?>
            </div>

            <div class="contact-form__form-message">
                <?php foreach ($messageFields as $fieldSid => $question): ?>
                    <?php $renderQuestion($fieldSid, $question); ?>
                <?php endforeach; ?>
            </div>

            <div class="contact-form__bottom">
                <p class="contact-form__bottom-policy">
                    Нажимая «Оставить заявку», вы подтверждаете, что ознакомлены, полностью согласны и принимаете условия
                    «Согласия на обработку персональных данных».
                </p>
                <button class="form-button contact-form__bottom-button" type="submit" name="web_form_submit" value="Y">
                    <span class="form-button__title">Оставить заявку</span>
                </button>
            </div>
        </div>

        <?php echo isset($arResult["FORM_FOOTER"]) ? $arResult["FORM_FOOTER"] : ""; ?>
    <?php endif; ?>
</section>
