<?php

namespace XIVDB\Routes\AppDashboard;

use Symfony\Component\HttpFoundation\Request;

use XIVDB\Apps\Translations\Translations;

//
// Home
//
trait AppTranslations
{
    protected function _translations()
    {
		//
		// translations
		//
        $this->route('/translations', 'GET', function(Request $request)
        {
            $this->mustBeModerator();

            return $this->respond('Dashboard/Translations/index.html.twig', []);
        });

        //
		// managing translations for a specific language
		//
        $this->route('/translations/manage/{language}', 'GET|POST', function(Request $request, $language)
        {
            $this->mustBeModerator();

            // translation class
            $TranslationsClass = new Translations();
            $TranslationsClass->setLanguage($language)->setRequest($request);

            // if saved changes
            if ($request->get('newchanges'))
            {
                $count = $TranslationsClass->updateBulk($request->get('newchanges'));
                $this->getModule('session')->add('success', sprintf('%s Translations have been saved.', $count));
            }

            // get translations
            $translations = $TranslationsClass->get();

            // get categories
            $categories = $TranslationsClass->getCategories();

            // get coverage
            $coverage = $TranslationsClass->getCoverage($translations);

            return $this->respond('Dashboard/Translations/manage.html.twig', [
                'language' => ucwords($language),
                'translations' => $translations,
                'categories' => $categories,
                'coverage' => $coverage,
                'category' => $request->get('category'),
            ]);
        });

        //
		// managing translations for a specific language
		//
        $this->route('/translations/ascode', 'GET', function(Request $request)
        {
            $this->mustBeModerator();

            // translation class
            $TranslationsClass = new Translations();
            $TranslationsClass->setLanguage('french')->setRequest($request);

            // get translations
            $translations = $TranslationsClass->get();

            return $this->respond('Dashboard/Translations/ascode.html.twig', [
                'translations' => $translations,
            ]);
        });

        //
		// edit a single translation
		//
        $this->route('/translations/{id}/update', 'GET|POST', function(Request $request, $id)
        {
            $this->mustBeModerator();

            $TranslationsClass = new Translations();
            $TranslationsClass->setRequest($request);

            if ($request->get('text_en')) {
                $TranslationsClass->update($id, [
                    'text_en' => $request->get('text_en'),
                    'text_fr' => $request->get('text_fr'),
                    'text_de' => $request->get('text_de'),
                    'text_ja' => $request->get('text_ja'),
                    'text_cns' => $request->get('text_cns'),
                    'define' => $request->get('define'),
                    'notes' => $request->get('notes'),
                    'category' => $request->get('category'),
                ]);

                $this->getModule('session')->add('success', 'Translation has been updated!');
            }

            // get translations
            $translation = $TranslationsClass->get($id);

            // get categories
            $categories = $TranslationsClass->getCategories();
            return $this->respond('Dashboard/Translations/edit.html.twig', [
                'translation' => $translation,
                'categories' => $categories,
            ]);
        });

        //
        // create a translation
        //
        $this->route('/translations/create', 'GET|POST', function(Request $request)
        {
            $this->mustBeModerator();

            $TranslationsClass = new Translations();
            $TranslationsClass->setRequest($request);
            $check = false;

            if ($request->get('text_en')) {
                // check the translation doesn't already exist
                $check = $TranslationsClass->search($request->get('text_en'));

                if (!$check) {
                    $insertId = $TranslationsClass->insert([
                        'text_en' => $request->get('text_en'),
                        'text_fr' => $request->get('text_fr'),
                        'text_de' => $request->get('text_de'),
                        'text_ja' => $request->get('text_ja'),
                        'text_cns' => $request->get('text_cns'),
                        'define' => $request->get('define'),
                        'notes' => $request->get('notes'),
                        'category' => $request->get('category'),
                    ]);

                    $this->getModule('session')
                        ->add('success', sprintf('Translation #%s has been added!', $insertId));
                }
            }

            // get categories
            $categories = $TranslationsClass->getCategories();
            return $this->respond('Dashboard/Translations/add.html.twig', [
                'insertId' => isset($insertId) ? $insertId : null,
                'categories' => $categories,
                'check' => $check,
            ]);
        });

        //
        // backups
        //
        $this->route('/translations/backups', 'GET', function(Request $request)
        {
            $this->mustBeModerator();

            $TranslationsClass = new Translations();
            $backups = $TranslationsClass->getBackups();

            // get categories
            return $this->respond('Dashboard/Translations/backups.html.twig', [
                'backups' => $backups,
            ]);
        });
    }
}
