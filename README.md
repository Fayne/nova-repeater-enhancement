<p align="center"><img src="http://res.cloudinary.com/guorenjun/image/upload/v1531809978/MN_LOGO_3.png" width="300"></p>

创建这个包的初衷是因为在Repeater field使用中发现，`Add item`弹出的DropdownMenu组件宽带只有120px, 现在改成通过`withMeta`方法动态控制Dropdown组件的宽度。

```php
<?php

Repeater::make('Components')
    ->withMeta(['dropdownWidth' => '240']) // 此处的值是整型或者字符串 'auto'
    ->repeatables([
        Repeaters\SomeRepeatableComponent::make(),
    ])->asJson()->fullWidth(),

```

详细可以参考以下[代码](/resources/js/components/FormField.vue#L44)

### Todo list

- [ ] Select组件需要做成searchable