<template>
  <DefaultField
      :field="currentField"
      :errors="errors"
      :show-help-text="showHelpText"
      :full-width-content="fullWidthContent"
  >
    <template #field>
      <div v-if="value.length > 0" class="space-y-4" :dusk="fieldAttribute">
        <RepeaterRow
            v-for="(item, index) in value"
            :dusk="`${index}-repeater-row`"
            :data-repeater-id="valueMap.get(item)"
            :item="item"
            :index="index"
            :key="valueMap.get(item)"
            @click="removeItem"
            :errors="errors"
            :sortable="currentField.sortable && value.length > 1"
            @move-up="moveUp"
            @move-down="moveDown"
            :field="currentField"
            :via-parent="fieldAttribute"
        />
      </div>
      <div>
        <div
            class="text-center"
            :class="{
            'bg-gray-50 dark:bg-gray-900 rounded-lg border-4 dark:border-gray-600 border-dashed py-3':
              value.length === 0,
          }"
        >
          <Dropdown v-if="currentField.repeatables.length > 1">
            <Button
                variant="link"
                leading-icon="plus-circle"
                trailing-icon="chevron-down"
            >
              {{ addItemText }}
            </Button>

            <template #menu>
              <!-- Search Input -->
              <div v-if="isSearchable" class="border-b border-gray-200 dark:border-gray-600">
                <input
                    type="text"
                    v-model="search"
                    :placeholder="__('Search items...')"
                    class="w-full px-3 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-gray-800 dark:text-white"
                    @input="performSearch"
                    @click.stop
                    @mousedown.stop
                    @focus.stop
                />
              </div>
              <DropdownMenu class="py-1" :width="dropdownWidth">
                <DropdownMenuItem
                    @click="() => addItem(repeatable.type)"
                    as="button"
                    v-for="repeatable in filteredRepeatables"
                    class="space-x-2"
                >
                  <span>
                    <Icon
                        :name="repeatable.icon"
                        type="solid"
                        class="inline-block"
                    />
                  </span>
                  <span>
                    {{ repeatable.singularLabel }}
                  </span>
                </DropdownMenuItem>
              </DropdownMenu>
            </template>
          </Dropdown>

          <InvertedButton
              v-else
              @click="addItem(currentField.repeatables[0].type)"
              type="button"
          >
            <span>{{
                __('Add :resource', {
                  resource: currentField.repeatables[0].singularLabel,
                })
              }}</span>
          </InvertedButton>
        </div>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { Button, Icon } from 'laravel-nova-ui'
import { FormField, HandlesValidationErrors } from '@/mixins'
import { computed } from 'vue'
import cloneDeep from 'lodash/cloneDeep'
import { uid } from 'uid/single'

export default {
  components: {
    Button,
    Icon,
  },

  mixins: [FormField, HandlesValidationErrors],

  provide() {
    return {
      removeFile: this.removeFile,
      shownViaNewRelationModal: computed(() => this.shownViaNewRelationModal),
      viaResource: computed(() => this.viaResource),
      viaResourceId: computed(() => this.viaResourceId),
      viaRelationship: computed(() => this.viaRelationship),
      resourceName: computed(() => this.resourceName),
      resourceId: computed(() => this.resourceId),
    }
  },

  data: () => ({
    valueMap: new WeakMap(),
    search: '',
  }),

  beforeMount() {
    this.value.map(repeatable => {
      this.valueMap.set(repeatable, uid())

      return repeatable
    })
  },

  methods: {
    /**
     * Return the field default value.
     */
    fieldDefaultValue() {
      return []
    },

    removeFile(attribute) {
      const {
        resourceName,
        resourceId,
        relatedResourceName,
        relatedResourceId,
        viaRelationship,
      } = this

      const uri =
          viaRelationship && relatedResourceName && relatedResourceId
              ? `/nova-api/${resourceName}/${resourceId}/${relatedResourceName}/${relatedResourceId}/field/${attribute}?viaRelationship=${viaRelationship}`
              : `/nova-api/${resourceName}/${resourceId}/field/${attribute}`

      Nova.request().delete(uri)
    },

    fill(formData) {
      this.finalPayload.forEach((repeatable, i) => {
        const attribute = `${this.fieldAttribute}[${i}]`
        formData.append(`${attribute}[type]`, repeatable.type)
        Object.keys(repeatable.fields).forEach(key => {
          formData.append(
              `${attribute}[fields][${key}]`,
              repeatable.fields[key]
          )
        })
      })
    },

    addItem(repeatableType) {
      const repeatable = this.currentField.repeatables.find(
          t => t.type === repeatableType
      )
      const copy = cloneDeep(repeatable)

      this.valueMap.set(copy, uid())

      this.value.push(copy)
    },

    removeItem(index) {
      const item = this.value.splice(index, 1)

      this.valueMap.delete(item)
    },

    moveUp(index) {
      const item = this.value.splice(index, 1)
      this.value.splice(Math.max(0, index - 1), 0, item[0])
    },

    moveDown(index) {
      const item = this.value.splice(index, 1)
      this.value.splice(Math.min(this.value.length, index + 1), 0, item[0])
    },

    performSearch(event) {
      this.search = event.target.value;
    },
  },

  computed: {
    finalPayload() {
      return this.value.map(repeatable => {
        const formData = new FormData()
        const fields = {}

        repeatable.fields.forEach(f => f.fill && f.fill(formData))

        for (const pair of formData.entries()) {
          fields[pair[0]] = pair[1]
        }

        return {type: repeatable.type, fields}
      })
    },

    dropdownWidth() {
      return this.currentField?.dropdownWidth || 'auto';
    },

    addItemText() {
      return this.currentField?.addItemText || this.__('Add item');
    },

    isSearchable() {
      return this.currentField?.searchable
    },

    /**
     * Return the field options filtered by the search string.
     *
     * @returns {Object[]}
     */
    filteredRepeatables() {
      return this.currentField.repeatables.filter(option => {
        if (!this.search) {
          return option;
        }

        return (
            option.label
                .toString()
                .toLowerCase()
                .indexOf(this.search.toLowerCase()) > -1
        )
      })
    },
  },
}
</script>
