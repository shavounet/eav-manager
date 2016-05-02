<?php

namespace CleverAge\EAVManager\AdminBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sidus\EAVModelBundle\Entity\DataInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class WysiwygController extends Controller
{
    /**
     * @Template()
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function selectDataAction(Request $request)
    {
        $formData = [
            'data' => $this->getData($request),
        ];
        $builder = $this->createFormBuilder($formData, [
            'show_legend' => false,
        ]);
        $builder->add('data', 'sidus_combo_data_selector');

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Template()
     * @param Request $request
     * @return array
     * @throws \Exception
     */
    public function selectMediaAction(Request $request)
    {
        $formData = [
            'data' => $this->getData($request),
            'filter' => $request->get('dataFilter'),
        ];
        $builder = $this->createFormBuilder($formData, [
            'show_legend' => false,
        ]);
        $builder->add('data', 'eavmanager_media_browser', [
            'family' => 'Image',
        ]);

        $filterConfig = $this->get('liip_imagine.filter.manager')->getFilterConfiguration()->all();
        $choices = array_combine(array_keys($filterConfig), array_keys($filterConfig));
        $builder->add('filter', 'choice', [
            'choices' => $choices,
        ]);

        $form = $builder->getForm();
        $form->handleRequest($request);

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @param Request $request
     * @return DataInterface|null
     * @throws \InvalidArgumentException
     */
    protected function getData(Request $request)
    {
        $data = null;
        if ($request->query->has('dataId')) {
            $data = $this->get('sidus_eav_model.doctrine.repository.data')
                ->find($request->query->get('dataId'));
        }
        return $data;
    }
}
