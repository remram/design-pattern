<?php

namespace Drupal\account\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\general\Service\TranslationService;
use Drupal\general\Service\ViewService;

/**
 * Class AccountController.
 *
 * Returns responses for Account routes.
 *
 * @package Drupal\account\Controller
 */
class AccountController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * @var \Drupal\general\Service\TranslationService $translationService
   */
  protected $translationService;

  /**
   * @var ViewService
   */
  protected $viewService;

  /**
   * AccountController constructor.
   *
   * @param \Drupal\general\Service\TranslationService $translationService
   * @param ViewService $viewService
   */
  public function __construct(TranslationService $translationService, ViewService $viewService){
    $this->translationService = $translationService;
    $this->viewService = $viewService;
  }

  /**
   * @param ContainerInterface $container
   * @return static
   */
  public static function create (ContainerInterface $container){
    return new static (
      $container->get('arc.general.translation_service'),
      $container->get('arc.general.view_service')
    );
  }
}
