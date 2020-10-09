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

type PhotoshootStatus = 'pending' | 'done';

export interface NewPhotoshoot {
  user: Pick<User, 'firstname' | 'email'>;
  date: Date;
  package: PhotoPackage;
  comment: string;
}

export interface PhotoshootDTO {
  id: string;
  customer: User;
  date: string;
  status: PhotoshootStatus;
  package: PhotoPackage;
  comment: string;
}

export interface Photoshoot {
  id: string;
  customer: User;
  date: Date;
  status: PhotoshootStatus;
  package: PhotoPackage;
  comment: string;
}

export interface NewSelectedPicture {
  id: string;
  filename: string;
}
