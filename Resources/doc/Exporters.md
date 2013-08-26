# Exporting the submitted form data

Since you will most likely want to do something with the submitted forms we've implemented a mechanism
to easily export the data on every FormSubmission or after the fact through a command.

A Zendesk exporter has been implemented already and can easily be activated on your custom FormPages.
Zendesk is a service that gives you a simple yet powerful interface to handle customer tickets.

## How does it work?

The trick with the exporters is pulling the correct data out of the FormSubmission entities so they can be exported.
We've implemented 2 mechanisms to do this.

### Using Identity Keys

By far the best way to do the matching is to set the entity_field on all FormPageParts.
This is basically just an identifier so we know what data is saved in what PagePart.
Obviously this mechanism will only work on  FormSubmission's created by correctly configured FormPages.

*note:* If you implement a custom FormPagePart make sure you set the ```identityKey``` property in the ```adaptForm``` method.
For example: ```$cfsf->setIdentityKey($this->getIdentityKey());```.

### Using the guesser mechanism

The other way is less accurate but provides an easy way to match FormPageParts for older formsubmissions.

All you have to do is implement the ```FormPageExportableInterface```'s ```getKeyGuessFieldNameMap``` method.
This method should return an array with the 'identity key' name as the key and either a regex string or an
array or regex strings that should match true if the AbstractFormPagePart's label text matches.

You can do this per language since the method receives a language string. However this is optional.
If you notice the labels are distinctive enough in each language this isn't necessary.

## Using the Zendesk exporter

TODO: Document what functions to add to a FormPage for it to find the key identifiers.
TODO: Document how to instantiate the ZendeskFormExporter as a service.

The ZendeskFormExporter requires a couple of Identity Keys to be present:

* email. The email at which the customer can be contacted.
* message. The message containing the customer's feedback.
* name or first_name and last_name. The customer's name. Can be provided in 2 fields.
* optionally: subject. If this is not filled in it'll grab the first 50 characters of the message.

Every other identity key present will be submitted as well but as a custom field on the ticket.
This allows you to filter out tickets in the backend or get a fully structured overview of what the customer has entered.

The language identity key is automatically set for every FormSubmission and will be entered in zendesk as a custom field.

## Exporting your backlog

Exporting the backlog is easy.

```
    app/console kuma:form:export --limit=1
```

The limit parameter can be 0 for no limit but this is definitely not recommended.
It's best to throttle it to 40 records every 10 minutes.
This way the limiter won't be hit when processing the entire backlog.

It's important to set this up as a cronjob because the API limit can be hit even when you don't have a backlog.
If this is the case new exports won't be submitted into Zendesk and you might lose cruacial customer feedback.

This is the reason why the export command has been created.
If this is set as a cronjob it'll continuously reattempt to submit the FormSubmissions that failed in the past.

## Adding your own exporters

Adding your own exporters is easy. Just implement the ```FormExportableInterface```,
register your new class as a service and tag it with ```kunstmaan_form.exporter```.

TODO: Document the API limit and serverside exceptions.
