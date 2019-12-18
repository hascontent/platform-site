This is a module for developers that allows the uses of the core `map` field in entities to be
edited in the entity form.  Here is an example from https://www.drupal.org/project/formassembly

```php
$fields['query_params'] = BaseFieldDefinition::create('map')
      ->setLabel(t('Query Parameters'))
      ->setDisplayOptions(
        'view',
        [
          'region' => 'hidden',
        ]
      )
      ->setDisplayOptions(
        'form',
        [
          'type' => 'map_assoc_widget',
          'region' => 'content',
          'settings' => [
            'size' => '40',
            'key_placeholder' => 'The tfa identifier',
            'value_placeholder' => 'Pre-filled value',
          ],
          'weight' => 90,
        ]
      )
      ->setTranslatable(FALSE)
      ->setDescription(
        'Enter parameters to be added to FormAssembly form request.<br />' .
        'The <em>tfa indentifier</em> string is the name property on the field\'s input tag.'
      )
      ->setDisplayConfigurable('form', TRUE)
      ->setDisplayConfigurable('view', FALSE);
```