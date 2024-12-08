import { Controller } from "@hotwired/stimulus"
import Formio from 'formiojs/Formio';

export default class ApplicationTaskEditController extends Controller {
    connect() {
       //  const formContainer = document.getElementById('formio')
       //  const formUrl = formContainer.getAttribute('data-url')
       //
       //  const formOptions = {
       //      hide: {
       //          "submit": true
       //      }
       //  }
       //
       //  const formio = new Formio(formUrl, formOptions);
       // console.log(formio);

        // formio.then((form) => {
        //     const button = document.querySelector('[data-action-name="saveAndReturn"]');
        //
        //     button.addEventListener('click', (event) => {
        //         if (!form.checkValidity(null, false, null, true)) {
        //             event.preventDefault()
        //             alert('The form is invalid!')
        //             return
        //         }
        //
        //         document.getElementById('ApplicationTask_submissionAsJsonString').value = JSON.stringify(form.submission.data)
        //     });
        //
        //     form.submission = {data: JSON.parse(formContainer.getAttribute('data-submission')) };
        //     form.nosubmit = true;
        // })
    }
}
