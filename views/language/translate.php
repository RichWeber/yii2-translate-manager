<?php
/**
 * @author Lajos Molnár <lajax.m@gmail.com>
 * @since 1.0
 */

use lajax\translatemanager\models\LanguageTranslate;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $language_id integer */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel lajax\translatemanager\models\searches\LanguageSourceSearch */

$this->title = Yii::t('language', 'Translation into {language_id}', ['language_id' => $language_id]);;
$this->params['breadcrumbs'][] = ['label' => Yii::t('language', 'Languages'), 'url' => ['list']];
$this->params['breadcrumbs'][] = $this->title;
?>
<h1>
<?= $this->title ?>
</h1>
    <?= Html::hiddenInput('language_id', $language_id, ['id' => 'language_id', 'data-url' => Yii::$app->urlManager->createAbsoluteUrl('/translatemanager/language/save')]); ?>
    <?= Html::hiddenInput('google_lang_id', $language_id, ['id' => 'google_lang_id', 'data-url' => Yii::$app->urlManager->createAbsoluteUrl('/translatemanager/language/google')]); ?>
    <?= Html::a(Yii::t('mycheck', 'Translate all'), Url::toRoute(['language/translate-all?' . Yii::$app->request->queryString]), ['class' => 'btn btn-success']) ?>
<div id="translates">

    <?php
    var_dump(Yii::$app->request->queryParams);
    var_dump(Yii::$app->request->queryString);
//    var_dump($language_id);
//    var_dump(Yii::$app->sourceLanguage);
//    var_dump(Yii::$app->i18n->translations['*']->sourceLanguage);
//    var_dump(Yii::$app->translatemanager);
//        var_dump(Yii::$app->i18n->translations['*']['sourceLanguage']);
//        var_dump($dataProvider);
    ?>
    <?php
    Pjax::begin([
        'id' => 'translates'
    ]);
    echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],
                'category',
                [
                    'format' => 'text',
                    'attribute' => 'message',
                    'filterInputOptions' => ['class' => 'form-control', 'id' => 'message'],
                    'label' => Yii::t('language', 'Source'),
                    'content' => function ($data) {
                        return Html::activeTextarea($data, 'message', ['name' => 'LanguageSource[' . $data->id . ']', 'class' => 'form-control source', 'readonly' => 'readonly']);
                    },
                ],
                [
                    'format' => 'text',
                    'attribute' => 'translation',
                    'filterInputOptions' => ['class' => 'form-control', 'id' => 'translation'],
                    'label' => Yii::t('language', 'Translation'),
                    'content' => function ($data) {
                        if ($data->languageTranslate === null) {
                            return Html::textarea('LanguageTranslate[' . $data->id . ']', '', ['class' => 'form-control translation', 'tabindex' => $data->id]);
                        }

                        return Html::activeTextarea($data->languageTranslate, 'translation', ['name' => 'LanguageTranslate[' . $data->id . ']', 'class' => 'form-control translation', 'data-id' => $data->id, 'tabindex' => $data->id]);
                    },
                ],
                [
                    'format' => 'text',
                    'filter' => LanguageTranslate::getStatusNames(),
                    'attribute' => 'is_translated',
                    'filterInputOptions' => ['class' => 'form-control', 'id' => 'is_translated'],
                    'label' => Yii::t('language', 'Status'),
                    'content' => function ($data) {
                        return Html::activeDropDownList($data, 'is_translated', LanguageTranslate::getStatusNames(), ['class' => 'is_translated', 'id' => $data['id'], 'data-url' => Yii::$app->urlManager->createUrl('/translatemanager/language/save')]);
                    },
                ],
                [
                    'format' => 'html',
                    'attribute' => Yii::t('language', 'Action'),
//                    'filterInputOptions' => ['class' => 'form-control', 'id' => 'google'],
                    'content' => function ($data) {
                        return Html::button(Yii::t('language', 'Save'), ['type' => 'button', 'data-id' => $data['id'], 'class' => 'btn btn-xs btn-success']) . ' ' .
                        Html::button(Yii::t('language', 'Google API'), ['type' => 'button', /*'id' => 'google',*/  'data-id' => $data['id'], 'class' => 'btn btn-xs btn-success google-btn']);
                    },
                ],
            ],
        ]);
        Pjax::end();
        ?>
</div>