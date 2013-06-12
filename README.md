Stawk
=====

Queue Duplication & Processing

Stawk will listen to a single queue, and distribute events to different queues
based on hooks. These can process data through custom classes, or simply pass
the data on to new queue service.

A full history of every processed event is stored in long term storage, which
allows for events to be pulled out and reprocessed.
