<?php

namespace Drupal\burger_api\Plugin\rest\resource;

use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\ResourceResponse;
use Drupal\Core\Menu\MenuTreeParameters;
use Drupal\Core\Cache\CacheableMetadata;

/**
 * Provides a resource to get Main and Footer menu items.
 *
 * @RestResource(
 *   id = "burger_navigation",
 *   label = @Translation("Burger Navigation Menu Items"),
 *   uri_paths = {
 *     "canonical" = "/api/navigation"
 *   }
 * )
 */
class NavigationResource extends ResourceBase {

  /**
   * Responds to GET requests.
   *
   * @return \Drupal\rest\ResourceResponse
   *   The response containing the menu trees.
   */
  public function get() {
    $data = [
      'main' => $this->getMenuTreeItems('main'),
      'footer' => $this->getMenuTreeItems('footer'),
    ];

    $response = new ResourceResponse($data, 200);

    // Apply cacheable metadata.
    $cacheability = new CacheableMetadata();
    $cacheability->addCacheTags([
      'config:system.menu.main',
      'config:system.menu.footer',
    ]);
    $response->addCacheableDependency($cacheability);

    return $response;
  }

  /**
   * Loads and formats a menu tree.
   *
   * @param string $menu_name
   *   The menu name.
   *
   * @return array
   *   The formatted menu items.
   */
  protected function getMenuTreeItems($menu_name) {
    $menu_tree_service = \Drupal::menuTree();
    $parameters = new MenuTreeParameters();
    $parameters->setMinDepth(1);

    // Load the tree.
    $tree = $menu_tree_service->load($menu_name, $parameters);

    // Transform/sort the tree and apply access checks.
    $manipulators = [
      ['callable' => 'menu.default_tree_manipulators:checkAccess'],
      ['callable' => 'menu.default_tree_manipulators:generateIndexAndSort'],
    ];
    $tree = $menu_tree_service->transform($tree, $manipulators);

    return $this->formatTree($tree);
  }

  /**
   * Helper to format the menu tree recursively.
   *
   * @param array $tree
   *   The menu tree.
   *
   * @return array
   *   The formatted menu items.
   */
  protected function formatTree(array $tree) {
    $items = [];
    foreach ($tree as $element) {
      if (!$element->link->isEnabled()) {
        continue;
      }

      $url = $element->link->getUrlObject();
      $url_string = '';

      if ($url->isExternal()) {
        $url_string = $url->getUri();
      }
      elseif (!$url->isRouted()) {
        try {
          $uri = $url->getUri();
          if (strpos($uri, 'internal:#') === 0) {
            $url_string = '#' . ltrim(substr($uri, 10));
          }
          elseif (strpos($uri, 'internal:') === 0) {
            $url_string = '/' . ltrim(substr($uri, 9));
          }
          elseif (strpos($uri, 'base:') === 0) {
            $url_string = '/' . ltrim(substr($uri, 5));
          }
          else {
            $url_string = $uri;
          }
        }
        catch (\Exception $e) {
          $url_string = '';
        }
      }
      else {
        try {
          $route_name = $url->getRouteName();
          if ($route_name === '<none>' || $route_name === '<nolink>') {
            $url_string = '#';
          }
          else {
            $url_string = $url->toString();
          }
        }
        catch (\Exception $e) {
          $url_string = '';
        }
      }

      $item = [
        'title' => $element->link->getTitle(),
        'description' => $element->link->getDescription(),
        'url' => $url_string,
        'weight' => $element->link->getWeight(),
        'external' => $url->isExternal(),
      ];

      if (!empty($element->below)) {
        $item['below'] = $this->formatTree($element->below);
      }

      $items[] = $item;
    }
    return $items;
  }

}
