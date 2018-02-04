<?php

namespace Drupal\umami_ga\EventSubscriber;

use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\ga\AnalyticsCommand\Set;
use Drupal\ga\AnalyticsEvents;
use Drupal\ga\Event\CollectEvent;
use Drupal\node\NodeInterface;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class AnalyticsSubscriber.
 */
class AnalyticsSubscriber implements EventSubscriberInterface {

  /**
   * The current route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  private $routeMatch;

  /**
   * AnalyticsSubscriber constructor.
   *
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The current route match service.
   */
  public function __construct(
    RouteMatchInterface $routeMatch
  ) {
    $this->routeMatch = $routeMatch;
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];

    $events[AnalyticsEvents::COLLECT][] = ['onAnalyticsCollect'];

    return $events;
  }

  /**
   * Set global analytics dimensions.
   *
   * @param \Drupal\ga\Event\CollectEvent $event
   *   The event to process.
   */
  public function onAnalyticsCollect(CollectEvent $event) {
    if (($node = $this->routeMatch->getParameter('node'))) {
      $this->applyAttributesFromNode($event, $node);
    }
    elseif (($term = $this->routeMatch->getParameter('taxonomy_term'))) {
      $this->applyAttributesFromTaxonomyTerm($event, $term);
    }
  }

  /**
   * Apply attributes from a node to analytics commands.
   *
   * @param \Drupal\ga\Event\CollectEvent $event
   *   The collect event to add commands to.
   * @param \Drupal\node\NodeInterface $node
   *   The node to determine attributes.
   */
  private function applyAttributesFromNode(CollectEvent $event, NodeInterface $node) {

    if (!empty($node->field_recipe_category[0]->entity)) {
      $category = $node->field_recipe_category[0]->entity->name->value;
      $event->addCommand(new Set('dimension1', $category));
    }

    if (!empty($node->field_difficulty)) {
      $difficulty = $node->field_difficulty->value;
      $event->addCommand(new Set('dimension2', $difficulty));
    }

    if ($node->bundle() == 'article') {
      $username = $node->getRevisionUser()->getAccountName();
      $event->addCommand(new Set('dimension3', $username));
    }
  }

  /**
   * Apply attributes from a taxonomy term to analytics commands.
   *
   * @param \Drupal\ga\Event\CollectEvent $event
   *   The collect event to add commands to.
   * @param \Drupal\taxonomy\TermInterface $term
   *   The taxonomy term to determine the attributes.
   */
  private function applyAttributesFromTaxonomyTerm(CollectEvent $event, TermInterface $term) {

    if ($term->bundle() == 'recipe_category') {
      $category = $term->name->value;
      $event->addCommand(new Set('dimension1', $category));
    }
  }

}
