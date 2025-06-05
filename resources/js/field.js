import DetailField from './components/DetailField'
import FormField from './components/FormField'

Nova.booting((app, store) => {
  app.component('detail-repeater', DetailField)
  app.component('form-repeater', FormField)
})
