# Increment
Wordpress plugin to create increment buttons via shortcodes.

## Shortcode
The shortcode `[increment-button]` requires at least some content and an argument to work :

| Parameter | Type     | Description                                      | Exemple                                                         |
| --------- | -------- | ------------------------------------------------ | --------------------------------------------------------------- |
| `content` | *string* | The button's inner content.                      | `[increment-button id="apples"]Add an apple[/increment-button]` |
| `id`      | *string* | Id of the value to be retrieved and incremented. | `id="visiteurs"`                                                |

The resulting code with the minimum parameters would be :
```html
<button class='increment-button increment-[id] '>[content]</button>
<span style='display:none;'>
  <span class='increment-response-value'>[value]</span>
</span>
```

These optionnal paramaters are also supported :

| Parameter          | Type         | Default | Description                                                                                             | Exemple                          |
| ------------------ | ------------ | ------- | ------------------------------------------------------------------------------------------------------- | -------------------------------- |
| `button_class`     | *string*     |         | CSS class to add to the button.                                                                         | `button_class="my-class"`        |
| `show_immediately` | `0` *or* `1` | `0`     | Whether to display the value on page load. If `0` the value will be shown after the first button press. | `value_show="1"`                 |
| `wrap_tag`         | *string*     | `span`  | Name of the tag wrapping the value.                                                                     | `wrap_tag="p"`                   |
| `wrap_class`       | *string*     |         | CSS class to add to the value wrapper.                                                                  | `wrap_class="my-other-class"`    |
| `before`           | *string*     |         | Content to add before the value (inside the wrapper).                                                   | `before="There was "`            |
| `after`            | *string*     |         | Content to add after the value (inside the wrapper).                                                    | `after=" visitors <i>today</i>"` |
