<?php

namespace Zippy\Html;

use \Zippy\Interfaces\EventReceiver;
use \Zippy\WebApplication;

/**
 * Компонент  реализующий  блок  страницы  с  собственным  HTML шаблоном
 * для  отображении на странице  как  обычного  компонента.
 * Позволяет отображать на  разных  страницах  типовые элементы
 * с тем  же содержанием. Напрмер меню, формы  логина и т.д.
 * Как  правило  используется  блочный тэг типа DIV
 *
 */
abstract class PageFragment extends HtmlContainer implements EventReceiver
{

        public $layout = '';

        /**
         * @see  HtmlComponent
         */
        public function Render()
        {
                // $template = 
                $htmltag = $this->getTag();

                $template = WebApplication::getApplication()->getTemplate(get_class($this));
                $qid = \phpQuery::getDocumentID($htmltag);

                $doc = \phpQuery::newDocumentHTML($template);

                $htmltag->replaceWith($doc['body']->html());
                //$htmltag->html($doc['body']->html());

                \phpQuery::selectDocument($qid);


                $this->beforeRender();

                parent::RenderImpl();
                $this->afterRender();
        }

        /**
         * Вызывается   страницей  владельцем  при обработке  HTTP запроса
         *
         */
        public function RequestHandle()
        {
                $this->beforeRequest();
                parent::RequestHandle();
                $this->afterRequest();
        }

        /**
         * Вызывается  перед  обработкой  HTTP   запроса 
         */
        public function beforeRequest()
        {
                
        }

        /**
         * Вызывается  после  обработки  HTTP  запроса
         */
        public function afterRequest()
        {
                
        }

        /**
         * Возвращает  страницу-владельца
         * @return WebPage
         */
        protected function getOwnerPage()
        {
                $owner = $this->getOwner();
                do {
                        if ($owner instanceof \Zippy\Html\WebPage) {
                                return $owner;
                        }
                        $owner = $owner->getOwner();
                } while ($owner != null);

            return null;
        }

        /**
         * Вызывается  перед  обработкой  HTTP   запроса страницы
         */
        public function beforeRequestPage()
        {
                
        }

        /**
         * вызывается после  обработки  HTTP   запроса страницы
         * 
         */
        public function afterRequestPage()
        {
                
        }

        protected function onAdded()
        {
                $page = $this->getOwnerPage();
                if ($page instanceof \Zippy\Html\WebPage) {
                        $page->addBeforeRequestEvent($this, 'beforeRequestPage');
                        $page->addAfterRequestEvent($this, 'afterRequestPage');
                }
        }

}

