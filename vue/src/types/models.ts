export interface User {
  id: string;
  email: string;
  roles: string[];
  firstname: string;
  lastname: string;
}

export interface PhotoPackage {
  id: string;
  name: string;
  quantity: number;
}

type PhotoshootStatus = 'pending';

export interface NewPhotoshoot {
  user: Pick<User, 'firstname' | 'lastname' | 'email'>;
  expiration: Date;
  package: string;
}

export interface PhotoshootDTO {
  id: string;
  customer: User;
  expiration: string;
  status: PhotoshootStatus;
  package: PhotoPackage;
}

export interface Photoshoot {
  id: string;
  customer: User;
  expiration: Date;
  status: PhotoshootStatus;
  package: PhotoPackage;
}
