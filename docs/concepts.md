# Sharp's concepts

Don't worry, there's not much.

## Entities
An *entity* is a business object that Sharp has to manipulate. Let's say, for example, a Client, or a Giraffe (in a zoo case).

## Categories
A *category* is just a place to store entities. So giraffes could be stored in a *Ruminant* category, or maybe in some *Africa* zoo department.

## Sublist
A *sublist* is used to group entities. This isn't required, be could be useful in some transversal cases. Take a theatre, for example: we have to manage some *event* or *show* entities, but we would likely group them in *seasons*. Well, in this case, season is a sublist, which can apply to several entities (events, but also tickets prices, and maybe blog posts, ...).

## Repository
This one is probably more obvious. Sharp needs *repositories* to work with, which are the abstraction layer between Sharp and the data. Those repos must implements specific interfaces.

## Validator
*Validators* are simple descriptive classes used while... validating data.

## Field
A *field* is obviously a web composant used to let a user enter data. There are many field types in Sharp, but we'll get to this later.

## Command
A *command* is a project specific action executed on an entity or on an entities list.