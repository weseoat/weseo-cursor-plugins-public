---
name: grill-me
description: Interview the user relentlessly about a plan or design until reaching shared understanding, resolving each branch of the decision tree. Use before WST Builder Section or CPT implementation starts, or when the user wants to stress-test a plan.
---

# Grill Me

Interview the maintainer relentlessly about every aspect of the plan until you reach shared understanding. Walk down each branch of the design tree and resolve dependencies between decisions one by one.

Ask one question at a time. For each question, provide your recommended answer.

If a question can be answered by exploring project-local context or the codebase, explore first instead of asking. Do not ask for values that are already recorded in Project Context, an issue brief, a concrete handoff, or nearby implementation patterns.

For WST Builder work, the grilling output must be a prefilled handoff draft, not only a chat summary:

- For Section work, create or update the prefilled Section handoff draft before changing Section templates, ACF structures, Flexible Content wiring, CSS hooks, or handoff content.
- For CPT work, create or update the dedicated CPT handoff draft before changing CPT registration, taxonomy setup, ACF field groups, WP Grid Builder foundations, card templates, archive/grid integration, optional single templates, CSS hooks, or handoff content.
- If a blocking project-specific value is missing, stop and ask for it or record an explicit unresolved placeholder in the draft. Do not invent project paths, URLs, ACF keys, field post IDs, WP Grid Builder IDs, selectors, storage locations, cache commands, or branch/PR carriers.
