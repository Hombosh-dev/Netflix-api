<?php

namespace App\Http\Controllers;

use App\Actions\Persons\CreatePerson;
use App\Actions\Persons\GetPersons;
use App\Actions\Persons\UpdatePerson;
use App\DTOs\Persons\PersonIndexDTO;
use App\DTOs\Persons\PersonStoreDTO;
use App\DTOs\Persons\PersonUpdateDTO;
use App\Http\Requests\Persons\PersonDeleteRequest;
use App\Http\Requests\Persons\PersonIndexRequest;
use App\Http\Requests\Persons\PersonStoreRequest;
use App\Http\Requests\Persons\PersonUpdateRequest;
use App\Http\Resources\MovieResource;
use App\Http\Resources\PersonResource;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PersonController extends Controller
{
    /**
     * Get paginated list of persons with filtering, sorting and pagination
     *
     * @param  PersonIndexRequest  $request
     * @param  GetPersons  $action
     * @return AnonymousResourceCollection
     */
    public function index(PersonIndexRequest $request, GetPersons $action): AnonymousResourceCollection
    {
        $dto = PersonIndexDTO::fromRequest($request);
        $persons = $action->handle($dto);

        return PersonResource::collection($persons);
    }

    /**
     * Get detailed information about a specific person
     *
     * @param  Person  $person
     * @return PersonResource
     */
    public function show(Person $person): PersonResource
    {
        return new PersonResource($person->load('movies'));
    }

    /**
     * Get movies associated with a specific person
     *
     * @param  Person  $person
     * @return AnonymousResourceCollection
     */
    public function movies(Person $person): AnonymousResourceCollection
    {
        $movies = $person->movies()->paginate();

        return MovieResource::collection($movies);
    }

    /**
     * Store a newly created person
     *
     * @param  PersonStoreRequest  $request
     * @param  CreatePerson  $action
     * @return PersonResource
     */
    public function store(PersonStoreRequest $request, CreatePerson $action): PersonResource
    {
        $dto = PersonStoreDTO::fromRequest($request);
        $person = $action->handle($dto);

        return new PersonResource($person);
    }

    /**
     * Update the specified person
     *
     * @param  PersonUpdateRequest  $request
     * @param  Person  $person
     * @param  UpdatePerson  $action
     * @return PersonResource
     */
    public function update(PersonUpdateRequest $request, Person $person, UpdatePerson $action): PersonResource
    {
        $dto = PersonUpdateDTO::fromRequest($request);
        $person = $action->handle($person, $dto);

        return new PersonResource($person);
    }

    /**
     * Remove the specified person
     *
     * @param  PersonDeleteRequest  $request
     * @param  Person  $person
     * @return JsonResponse
     */
    public function destroy(PersonDeleteRequest $request, Person $person): JsonResponse
    {
        // Check if the person has related movies
        if ($person->movies()->exists()) {
            return response()->json([
                'message' => 'Cannot delete person with associated movies. Remove associations first.',
            ], 422);
        }

        $person->delete();

        return response()->json([
            'message' => 'Person deleted successfully',
        ]);
    }
}
