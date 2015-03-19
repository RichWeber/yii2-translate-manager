<?php

namespace lajax\translatemanager\controllers\actions;

use Yii;
use lajax\translatemanager\bundles\TranslateAsset;
use lajax\translatemanager\bundles\TranslatePluginAsset;
use lajax\translatemanager\models\searches\LanguageSourceSearch;
use lajax\translatemanager\models\LanguageTranslate;
use yii\helpers\Url;

/**
 * This class facilitates the listing of language elements to be translated.
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */
class TranslateAllAction extends \yii\base\Action {

    /**
     * @inheritdoc
     */
    public function init() {

        TranslateAsset::register(Yii::$app->controller->view);
        TranslatePluginAsset::register(Yii::$app->controller->view);
        parent::init();
    }

    /**
     * Translate of language elements.
     * @return string
     */
    public function run() {

        $searchModel = new LanguageSourceSearch;
        $dataProvider = $searchModel->search(Yii::$app->request->getQueryParams());
        $sources = $dataProvider->getModels();
        $languageId = Yii::$app->request->get('language_id', '');

        if (!is_null($sources)) {
            foreach ($sources as $source) {

                $languageTranslate = LanguageTranslate::findOne(['id' => $source->id, 'language' => $languageId]) ? :
                    new LanguageTranslate(['id' => $source->id, 'language' => $languageId]);

                if (!$languageTranslate->is_translated) {
                    $google = Yii::$app->translate->translate(
                        substr(Yii::$app->i18n->translations['*']->sourceLanguage, 0, 2),
                        substr($languageId, 0, 2),
                        $source->message
                    );

                    if (!isset($google['data']['translations'][0]['translatedText'])
                        || empty($google['data']['translations'][0]['translatedText'])
                    ) {
                        return ['Unabled make translate whith Google Translate API'];
                    }

                    $languageTranslate->translation = $google['data']['translations'][0]['translatedText'];
                    $languageTranslate->is_translated = 1;
                    if ($languageTranslate->validate() && $languageTranslate->save()) {
                        //
                    }
                }
            }
        }

        return Yii::$app->controller->redirect(Url::toRoute(['language/translate?' . Yii::$app->request->queryString]));
    }

}
